<?php
header('Content-Type: application/json');
require_once(dirname(__DIR__) . '/models/funcoes_usuario.php');

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'mensagem' => 'Método de requisição inválido.']);
    exit();
}

// Obtém os dados do formulário
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : '';
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['senha']) ? $_POST['senha'] : '';
$confirmar_senha = isset($_POST['confirmar_senha']) ? $_POST['confirmar_senha'] : '';

// Valida os campos obrigatórios
if (empty($nome) || empty($cpf) || empty($telefone) || empty($email) || empty($senha) || empty($confirmar_senha)) {
    echo json_encode(['status' => false, 'mensagem' => 'Por favor, preencha todos os campos.']);
    exit();
}

// Valida o formato do e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => false, 'mensagem' => 'Por favor, informe um e-mail válido.']);
    exit();
}

// Valida o CPF
if (!validarCPF($cpf)) {
    echo json_encode(['status' => false, 'mensagem' => 'Por favor, informe um CPF válido.']);
    exit();
}

// Valida o telefone
$telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
if (strlen($telefone_limpo) < 10) {
    echo json_encode(['status' => false, 'mensagem' => 'Por favor, informe um telefone válido.']);
    exit();
}

// Valida o domínio do e-mail
if (!validarDominioEmail($email)) {
    echo json_encode(['status' => false, 'mensagem' => 'Domínio de e-mail inválido. Tente algo como Ex: usuario@gmail.com.']);
    exit();
}

// Valida o nome (mínimo 2 palavras)
if (!temMinimoDuasPalavras($nome)) {
    echo json_encode(['status' => false, 'mensagem' => 'O nome deve conter pelo menos 2 palavras.']);
    exit();
}

// Verifica se as senhas coincidem
if ($senha !== $confirmar_senha) {
    echo json_encode(['status' => false, 'mensagem' => 'As senhas não coincidem.']);
    exit();
}

// Valida a senha (mínimo 7 caracteres)
if (strlen($senha) < 7) {
    echo json_encode(['status' => false, 'mensagem' => 'A senha deve ter pelo menos 7 caracteres.']);
    exit();
}

// Criptografa a senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Tenta cadastrar o usuário
$resultado = cadastrarUsuario($nome, $cpf, $telefone, $email, $senha_hash);

// Retorna o resultado
echo json_encode($resultado);
