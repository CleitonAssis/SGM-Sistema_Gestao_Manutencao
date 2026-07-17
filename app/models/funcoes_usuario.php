<?php
require_once(dirname(__DIR__) . '/config/database.php');
require_once(dirname(__DIR__) . '/models/enviar_email.php');

/**
 * Cadastra um novo usuário no sistema
 * 
 * @param string $nome Nome completo do usuário
 * @param string $cpf CPF do usuário (formato: 000.000.000-00)
 * @param string $telefone Telefone do usuário (formato: (00) 00000-0000)
 * @param string $email Email do usuário
 * @param string $senha Senha do usuário (será criptografada)
 * @return array Array com status da operação e mensagem
 */
function cadastrarUsuario($nome, $cpf, $telefone, $email, $senha_hash)
{
    // Formata o nome do usuário no padrão "Nome Sobrenome"
    $nome_formatado = normalizarNome($nome);

    $conn = conectarBD();

    // Verifica se o token de validação de conta existe e está expirado através do CPF e e-mail
    $stmt = $conn->prepare("SELECT id, token_validacao, data_hora_expiracao_token FROM usuarios WHERE cpf = ? AND email = ? AND validado = 0 AND data_hora_expiracao_token < NOW()");
    $stmt->bind_param("ss", $cpf, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $id = $usuario['id'];
        $tokenValidacao = bin2hex(random_bytes(16)); // 16 bytes = 32 caracteres hexadecimais
        $validado = 0;
        $dataHoraExpiracaoToken = date('Y-m-d H:i:s', strtotime('+1 hour')); // 1 hora a partir de agora

        // Atualiza a senha, token de validação, status de validação e data de expiração no banco de dados
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, senha = ?, telefone = ?, token_validacao = ?, validado = ?, data_hora_expiracao_token = ? WHERE id = ?");
        $stmt->bind_param("ssssisi", $nome_formatado, $senha_hash, $telefone, $tokenValidacao, $validado, $dataHoraExpiracaoToken, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            $conn->close();

            // Envia email de confirmação
            $resultado_email = enviarEmailValidacaoConta($nome_formatado, $email, $tokenValidacao);
            if ($resultado_email['status']) {
                return ['status' => true, 'mensagem' => 'Link de validação da conta renovado! Confira seu e-mail para ativar a conta.'];
            } else {
                return ['status' => false, 'mensagem' => 'Erro ao enviar e-mail de confirmação.'];
            }
        } else {
            $stmt->close();
            $conn->close();
            return ['status' => false, 'mensagem' => 'Erro ao renovar o link de validação da conta.'];
        }
    }

    // Remove caracteres especiais do CPF e telefone
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    $telefone = preg_replace('/[^0-9]/', '', $telefone);

    // Formata CPF e telefone para exibição
    $cpf_formatado = formatarCPF($cpf);
    $telefone_formatado = formatarTelefone($telefone);


    // Verifica se o email já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return ['status' => false, 'mensagem' => 'Usuário já está cadastrado.'];
    }

    // Verifica se o CPF já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE cpf = ?");
    $stmt->bind_param("s", $cpf_formatado);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return ['status' => false, 'mensagem' => 'Usuário já está cadastrado.'];
    }

    // Gera token de validação
    $tokenValidacao = bin2hex(random_bytes(16)); // 16 bytes = 32 caracteres hexadecimais

    // Define a data de expiração do token de validação
    $minutos = 1440; // 24 horas
    $expiracao = date('Y-m-d H:i:s', time() + ($minutos * 60));

    // Insere o usuário no banco de dados
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, cpf, telefone, email, senha, token_validacao, data_hora_expiracao_token, data_hora_cadastro) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssss", $nome_formatado, $cpf_formatado, $telefone_formatado, $email, $senha_hash, $tokenValidacao, $expiracao);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $conn->close();

        // Envia email de confirmação
        $resultado_email = enviarEmailValidacaoConta($nome_formatado, $email, $tokenValidacao);

        if ($resultado_email['status']) {
            return ['status' => true, 'mensagem' => 'Cadastro realizado! Verifique seu e-mail para confirmar a conta.', 'nome' => $nome_formatado, 'email' => $email, 'token' => $tokenValidacao];
        } else {
            return ['status' => true, 'mensagem' => 'Cadastro realizado! O e-mail de confirmação não foi enviado: ' . $resultado_email['mensagem'], 'nome' => $nome, 'email' => $email, 'token' => $tokenValidacao];
        }
    } else {
        $stmt->close();
        $conn->close();
        return ['status' => false, 'mensagem' => 'Erro ao cadastrar usuário.'];
    }
}

/**
 * Recupera a senha do usuário
 * 
 * @param string $email Email do usuário
 * @return array Array com status da operação e mensagem
 */
function recuperarSenha($cpf, $email)
{
    $conn = conectarBD();

    // Verifica se o email e o CPF estão corretos
    $stmt = $conn->prepare("SELECT id, nome, validado FROM usuarios WHERE cpf = ? AND email = ?");
    $stmt->bind_param("ss", $cpf, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $usuario_id = $usuario['id'];
    } else {
        $stmt->close();
        $conn->close();
        return ['status' => false, 'mensagem' => 'Usuário não encontrado. Verifique o CPF e o e-mail informados.'];
    }

    // Verifica se o usuário está validado
    if ($usuario['validado'] === 0) {
        $stmt->close();
        $conn->close();
        return ['status' => false, 'mensagem' => 'Conta ainda não foi validada. Por favor, verifique seu e-mail.'];
    }

    // Verifica se há um token de recuperação ativo
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND token_recuperacao IS NOT NULL AND data_hora_expiracao_token > NOW()");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return ['status' => false, 'mensagem' => 'Já existe um processo de recuperação de senha em andamento. Por favor, tente novamente mais tarde.'];
    }

    // Gera token de recuperação
    $tokenRecuperacao = bin2hex(random_bytes(16)); // 16 bytes = 32 caracteres hexadecimais

    // Define a data de expiração do token (1 hora a partir da criação)
    $minutos = 60; // 1 hora
    $expiracao = date('Y-m-d H:i:s', time() + ($minutos * 60));

    // Atualiza o token no banco de dados
    $stmt = $conn->prepare("UPDATE usuarios SET token_recuperacao = ?, data_hora_expiracao_token = ? WHERE id = ?");
    $stmt->bind_param("ssi", $tokenRecuperacao, $expiracao, $usuario_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Envia email de recuperação
        $resultado_email = enviarEmailRecuperacaoSenha($usuario['nome'], $email, $tokenRecuperacao);

        if ($resultado_email['status']) {
            return ['status' => true, 'mensagem' => 'Um link de recuperação foi enviado para o seu e-mail.'];
        } else {
            return ['status' => false, 'mensagem' => 'Erro ao enviar e-mail: ' . $resultado_email['mensagem']];
        }
    } else {
        return ['status' => false, 'mensagem' => 'Erro ao processar solicitação: ' . $conn->error];
    }
}

/**
 * Valida o login do usuário
 * 
 * @param string $email Email do usuário
 * @param string $senha Senha do usuário
 * @return array Array com status da operação e dados do usuário se bem-sucedido
 */
function loginUsuario($email, $senha)
{
    $conn = conectarBD();

    $stmt = $conn->prepare("SELECT id, nome, telefone, email, senha, token_validacao, validado FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        // Verifica se a conta foi validada
        if ((int)$usuario['validado'] !== 1 && $usuario['token_validacao'] !== null) {
            $stmt->close();
            $conn->close();
            return ['status' => false, 'mensagem' => 'Sua conta ainda não foi validada. Por favor, verifique seu e-mail.'];
        }
        // Verifica se a senha está correta
        if (password_verify($senha, $usuario['senha'])) {
            // Remove a senha do array antes de retornar
            unset($usuario['senha']);

            $stmt = $conn->prepare("SELECT token_recuperacao, data_hora_expiracao_token FROM usuarios WHERE id = ? AND token_recuperacao IS NOT NULL AND data_hora_expiracao_token IS NOT NULL");
            $stmt->bind_param("i", $usuario['id']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $stmt = $conn->prepare("UPDATE usuarios SET token_recuperacao = NULL, data_hora_expiracao_token = NULL WHERE id = ?");
                $stmt->bind_param("i", $usuario['id']);
                $stmt->execute();
                if ($stmt->affected_rows === 0) {
                    $stmt->close();
                    $conn->close();
                    return ['status' => false, 'mensagem' => 'Erro ao limpar token de redefinição de senha.'];
                }
            }

            $stmt->close();
            $conn->close();
            return ['status' => true, 'mensagem' => 'Login realizado com sucesso!', 'usuario' => $usuario];
        } else {
            $stmt->close();
            $conn->close();
            return ['status' => false, 'mensagem' => 'Senha incorreta.'];
        }
    } else {
        $stmt->close();
        $conn->close();
        return ['status' => false, 'mensagem' => 'Usuário não cadastrado.'];
    }
}

/**
 * Encerra a sessão do usuário
 * 
 * @return bool True se a sessão foi encerrada com sucesso
 */
function logoutUsuario(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Limpa todas as variáveis de sessão
    session_unset();

    // Destrói a sessão
    session_destroy();

    // Destrói o cookie da sessão
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    return true;
}

/**
 * Valida a conta do usuário através do token de validação
 * 
 * @param string $token Token de validação
 * @return array Array com status da operação e mensagem
 */
function validarConta($token)
{

    $conn = conectarBD();
    // Verifica se o token é válido e não expirou
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE token_validacao = ? AND data_hora_expiracao_token > NOW() AND validado = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        // Atualiza o status de validação do usuário para validado e remove o token de validação e data de expiração
        $stmt = $conn->prepare("UPDATE usuarios SET validado = 1, token_validacao = NULL, data_hora_expiracao_token = NULL WHERE id = ?");
        $stmt->bind_param("i", $usuario['id']);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['status' => true, 'mensagem' => 'Você já pode fazer login!'];
        } else {
            $stmt->close();
            $conn->close();
            return ['status' => false, 'mensagem' => 'Erro ao validar conta: ' . $conn->error];
        }
    } else {
        $stmt->close();
        $conn->close();
        return ['status' => false, 'mensagem' => 'Token inválido ou expirado.'];
    }
}

/**
 * Redefine a senha do usuário através do token de redefinição
 * 
 * @param string $token Token de redefinição
 * @param string $nova_senha Nova senha
 * @param string $confirmar_senha Confirmação da nova senha
 * @return array Array com status da operação e mensagem
 */
function redefinirSenha($token, $nova_senha_hash)
{
    $conn = conectarBD();

    // Verifica se o token é válido e não expirou
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE token_recuperacao = ? AND data_hora_expiracao_token > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows === 1) {
        // Busca o usuário pelo token
        $usuario = $result->fetch_assoc();
        $stmt->close();

        // Atualiza a senha no banco de dados e limpa o token de recuperação
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ?, token_recuperacao = NULL, data_hora_expiracao_token = NULL WHERE id = ?");
        $stmt->bind_param("si", $nova_senha_hash, $usuario['id']);
        if ($stmt->execute()) {
            return ['status' => true, 'mensagem' => 'Senha redefinida com sucesso!'];
        } else {
            return ['status' => false, 'mensagem' => 'Erro ao redefinir senha: ' . $conn->error];
        }
    } else {
        return ['status' => false, 'mensagem' => 'Token inválido ou expirado.'];
    }
}

/**
 * Formata o CPF para o padrão 000.000.000-00
 * 
 * @param string $cpf CPF sem formatação
 * @return string CPF formatado
 */
function formatarCPF($cpf)
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

/**
 * Formata o telefone para o padrão (00) 00000-0000
 * 
 * @param string $telefone Telefone sem formatação
 * @return string Telefone formatado
 */
function formatarTelefone($telefone)
{
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    $tamanho = strlen($telefone);

    if ($tamanho === 11) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7, 4);
    } elseif ($tamanho === 10) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6, 4);
    } else {
        return $telefone; // Retorna sem formatação se não for um número válido
    }
}

// Valida o CPF
function validarCPF($cpf)
{
    // Remove caracteres especiais
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se todos os dígitos são iguais
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    // Calcula o primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }
    $resto = $soma % 11;
    $dv1 = ($resto < 2) ? 0 : 11 - $resto;

    // Verifica o primeiro dígito verificador
    if ($cpf[9] != $dv1) {
        return false;
    }

    // Calcula o segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 10; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }
    $resto = $soma % 11;
    $dv2 = ($resto < 2) ? 0 : 11 - $resto;

    // Verifica o segundo dígito verificador
    if ($cpf[10] != $dv2) {
        return false;
    }

    return true;
}

// Valida o e-mail com domínios específicos
function validarDominioEmail($email)
{
    // Lista de domínios permitidos
    $dominiosPermitidos = [
        'gmail.com',
        'yahoo.com',
        'hotmail.com',
        'live.com',
        'outlook.com',
        'icloud.com',
        'belooriente.mg.gov.br',
        'grr.la'
    ];

    // Regex para validar e-mail com domínio específico
    $regex = '/^[a-zA-Z0-9._%+-]+@(' . implode('|', array_map('preg_quote', $dominiosPermitidos)) . ')$/';

    return preg_match($regex, $email) === 1;
}

// Testes
//var_dump(validarDominioEmail('usuario@gmail.com'));  // true
//var_dump(validarDominioEmail('usuario@meusite.com')); // false
//var_dump(validarDominioEmail('teste@hotmail.com'));   // true

// Formata o nome do usuário no padrão "Nome Sobrenome"
function normalizarNome($nome)
{
    // Remove espaços extras
    $nome = trim($nome);
    // Limita a 255 caracteres
    $nome = mb_substr($nome, 0, 255, 'UTF-8');
    // Converte tudo para minúsculo
    $nome = mb_strtolower($nome, 'UTF-8');
    // Coloca a primeira letra de cada palavra em maiúsculo
    $nome = mb_convert_case($nome, MB_CASE_TITLE, 'UTF-8');
    // Preposições que devem ficar em minúsculo
    $preposicoes = [' De ', ' Da ', ' Do ', ' Dos ', ' Das ', ' E '];
    foreach ($preposicoes as $p) {
        $nome = str_replace($p, mb_strtolower($p, 'UTF-8'), $nome);
    }
    return $nome;
}

// Verifica se o nome contém pelo menos duas palavras
function temMinimoDuasPalavras($nome)
{
    // Remove espaços extras no início e no fim
    $nome = trim($nome);

    // Divide a string por um ou mais espaços
    $palavras = preg_split('/\s+/', $nome);

    // Conta as palavras
    return count($palavras) >= 2;
}

