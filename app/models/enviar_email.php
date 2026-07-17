<?php
// Inclui as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Carrega o autoloader do Composer
require_once(dirname(__DIR__) . '/vendor/autoload.php');

// Verifica se o servidor está rodando em HTTPS
function getProtocolo() {
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        return 'https';
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return 'https';
    } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        return 'https';
    } else {
        return 'http';
    }
}

//$host = $_SERVER['HTTP_HOST']; // Domínio
//$requestUri = $_SERVER['REQUEST_URI']; // Caminho da URL

// Monta a URL completa
// $url = $protocolo . '://' . $host . $requestUri;

/**
 * Envia e-mail de confirmação de cadastro
 * 
 * @param string $nome Nome do destinatário
 * @param string $email Email do destinatário
 * @param string $token Token de validação
 * @return array Array com status da operação e mensagem
 */
function enviarEmailValidacaoConta($nome, $email, $tokenValidacao)
{
    // Cria uma instância do PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor
        $mail->isSMTP();                                      // Usa SMTP para envio
        $mail->Host       = 'smtp.umbler.com';               // Servidor SMTP (altere para o seu servidor)
        $mail->SMTPAuth   = true;                             // Habilita autenticação SMTP
        $mail->Username   = 'suporte@cvacentertech.com.br';          // Usuário SMTP (altere para o seu email)
        $mail->Password   = '3K?]@jCTMwh9';                      // Senha SMTP (altere para sua senha)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Habilita criptografia TLS
        $mail->Port       = 587;                              // Porta TCP para conexão
        $mail->CharSet    = 'UTF-8';                          // Define o charset como UTF-8

        // Destinatários
        $mail->setFrom('suporte@cvacentertech.com.br', 'Sistema Gestão de Manutenção');  // Remetente (altere para o seu email)
        $mail->addAddress($email, $nome);                           // Destinatário

        // Conteúdo do email
        $mail->isHTML(true);                                  // Define formato do email como HTML
        $mail->Subject = 'SGM-Confirmação de Cadastro';           // Assunto do email

        // Verifica se a requisição é HTTPS
        $protocolo = getProtocolo();
        /*if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $protocolo = 'https';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            // Caso esteja por trás de um proxy que use HTTP_X_FORWARDED_PROTO
            $protocolo = 'https';
        } elseif ($_SERVER['SERVER_PORT'] == 443) {
            // Verifica a porta do servidor
            $protocolo = 'https';
        } else {
            $protocolo = 'http';
        }*/

        // URL de validação
        $url_validacao = $protocolo . '://' . $_SERVER['HTTP_HOST'] . '/sgm/validar_conta?token=' . $tokenValidacao;

        // Corpo do email em HTML
        $mail->Body = templateEmailValidacaoConta($nome, $url_validacao);

        // Versão em texto puro para clientes de email que não suportam HTML
        $mail->AltBody = "Olá $nome,\n\nObrigado por se cadastrar em nosso sistema!\n\n"
            . "Para confirmar seu cadastro, acesse o link abaixo:\n\n"
            . "$url_validacao\n\n"
            . "Se você não solicitou este cadastro, por favor ignore este email.\n\n"
            . "Atenciosamente,\nEquipe Sistema Gestão de Manutenção";

        $mail->send();
        return ['status' => true];
    } catch (Exception $e) {
        return ['status' => false, 'mensagem' => "Erro ao enviar e-mail: {$mail->ErrorInfo}"];
    }
}

/**
 * Obtém o template HTML do email de confirmação
 * 
 * @param string $nome Nome do destinatário
 * @param string $url_validacao URL para validação da conta
 * @return string Template HTML do email
 */
function templateEmailValidacaoConta($nome, $url_validacao)
{
    // Template HTML do email
    $html = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Confirmação de Cadastro</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333333;
                max-width: 600px;
                margin: 0 auto;
            }
            .container {
                border: 1px solid #dddddd;
                border-radius: 5px;
                padding: 20px;
                margin: 20px 0;
            }
            .header {
                background-color: #17a2b8;
                color: #fff;
                padding: 10px;
                text-align: center;
                border-radius: 5px 5px 0 0;
            }
            .content {
                padding: 20px;
            }
            .button {
                display: inline-block;
                padding: 0.5rem 1rem;
                font-size: 1.25rem;
                line-height: 1.5;
                border-radius: 0.3rem;
                color: #fff !important;
                background-color: #17a2b8;
                border-color: #17a2b8;
                text-align: center;
                text-decoration: none;
                vertical-align: middle;
                cursor: pointer;
                -webkit-user-select: none;
                -moz-user-select: none;
                user-select: none;
                border: 1px solid transparent;
                transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            
            .button:hover {
                color: #fff !important;
                background-color: #138496;
                border-color: #138496;
                text-decoration: none;
            }
            
            .button:active {
                color: #fff !important;
                background-color: #138496;
                border-color: #138496;
                text-decoration: none;
                box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            }
            .footer {
                font-size: 12px;
                color: #777777;
                text-align: center;
                margin-top: 20px;
            }
            .h3 {
                font-size: 1rem;
                margin-top: 0;
                margin-bottom: 0.5rem;
                font-weight: 500;
                line-height: 1.2;
            }
            a {
                color: #17a2b8 !important;
                text-decoration: none;
                background-color: transparent;
                -webkit-text-decoration-skip: objects;
            }
            a:hover {
                color: #138496 !important;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="container h3">
            <div class="header">
                <h2>Confirmação de Cadastro</h2>
            </div>
            <div class="content">
                <p>Olá <strong>{$nome}</strong>,</p>
                <p>Obrigado por se cadastrar no sistema!</p>
                <p>Para confirmar seu cadastro e ativar sua conta, clique no botão abaixo:</p>
                
                <a href="{$url_validacao}" class="button">Confirmar Cadastro</a>
                
                <p>Ou acesse o link: <a href="{$url_validacao}">{$url_validacao}</a></p>
                <p>Se você não solicitou o cadastro, por favor ignore este email.</p>
                <p>Atenciosamente,<br>Equipe Sistema Gestão de Manutenção</p>
            </div>
            <div class="footer">
                <p>Este é um email automático. Por favor, não responda.</p>
            </div>
        </div>
    </body>
    </html>
    HTML;

    return $html;
}

/**
 * Envia email de recuperação de senha
 * 
 * @param string $email Email do usuário
 * @param string $token Token de recuperação
 * @return array Array com status da operação e mensagem
 */
function enviarEmailRecuperacaoSenha($nome, $email, $tokenRecuperacao)
{
    // Cria uma instância do PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor (mesmas do email de confirmação)
        $mail->isSMTP();
        $mail->Host       = 'smtp.umbler.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'suporte@cvacentertech.com.br';
        $mail->Password   = '3K?]@jCTMwh9';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Destinatários
        $mail->setFrom('suporte@macsystems.com.br', 'Sistema Gestão de Manutenção');
        $mail->addAddress($email, $nome);

        // Conteúdo do email
        $mail->isHTML(true);
        $mail->Subject = 'SGM-Recuperação de Senha';

        // Verifica se a requisição é HTTPS
        $protocolo = getProtocolo();

        // URL de recuperação
        $url_recuperacao = $protocolo . '://' . $_SERVER['HTTP_HOST'] . '/sgm/redefinir_senha?token=' . $tokenRecuperacao;

        // Corpo do email em HTML
        $mail->Body = templateEmailRecuperacaoSenha($nome, $url_recuperacao);

        // Versão em texto puro
        $mail->AltBody = "Olá {$nome},\n\nRecebemos uma solicitação para redefinir sua senha.\n\n"
            . "Para redefinir sua senha, acesse o link abaixo:\n\n"
            . "{$url_recuperacao}\n\n"
            . "Se você não solicitou esta alteração, por favor ignore este e-mail.\n\n"
            . "Este link expirará em 1 hora.\n\n"
            . "Este é um e-mail automático, por favor não responda."
            . "Se você não solicitou este cadastro, por favor ignore este email.\n\n"
            . "Atenciosamente,\nEquipe Sistema Gestão de Manutenção";

        // Envia o email
        $mail->send();
        return ['status' => true, 'mensagem' => 'E-mail de recuperação enviado com sucesso.'];
    } catch (Exception $e) {
        return ['status' => false, 'mensagem' => "Erro ao enviar e-mail: {$mail->ErrorInfo}"];
    }
}

/**
 * Obtém o template HTML do email de recuperação
 * 
 * @param string $nome Nome do destinatário
 * @param string $url_recuperacao URL para recuperação de senha
 * @return string Template HTML do email
 */
function templateEmailRecuperacaoSenha($nome, $url_recuperacao)
{
    // Template HTML do email
    $html = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>SGM-Recuperação de Senha</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333333;
                max-width: 600px;
                margin: 0 auto;
            }
            .container {
                border: 1px solid #dddddd;
                border-radius: 5px;
                padding: 20px;
                margin: 20px 0;
            }
            .header {
                background-color: #17a2b8;
                color: #fff;
                padding: 10px;
                text-align: center;
                border-radius: 5px 5px 0 0;
            }
            .content {
                padding: 20px;
            }
            .button {
                display: inline-block;
                padding: 0.5rem 1rem;
                font-size: 1.25rem;
                line-height: 1.5;
                border-radius: 0.3rem;
                color: #fff !important;
                background-color: #17a2b8;
                border-color: #17a2b8;
                text-align: center;
                text-decoration: none;
                vertical-align: middle;
                cursor: pointer;
                -webkit-user-select: none;
                -moz-user-select: none;
                user-select: none;
                border: 1px solid transparent;
                transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            
            .button:hover {
                color: #fff !important;
                background-color: #138496;
                border-color: #138496;
                text-decoration: none;
            }
            
            .button:active {
                color: #fff !important;
                background-color: #138496;
                border-color: #138496;
                text-decoration: none;
                box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            }
            .footer {
                font-size: 12px;
                color: #777777;
                text-align: center;
                margin-top: 20px;
            }
            .h3 {
                font-size: 1rem;
                margin-top: 0;
                margin-bottom: 0.5rem;
                font-weight: 500;
                line-height: 1.2;
            }
            a {
                color: #17a2b8 !important;
                text-decoration: none;
                background-color: transparent;
                -webkit-text-decoration-skip: objects;
            }
            a:hover {
                color: #138496 !important;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="container h3">
            <div class="header">
                <h2>Recuperação de Senha</h2>
            </div>
            <div class="content">
                <p>Olá, <strong>{$nome}</strong>!</p>
                <p>Recebemos uma solicitação para redefinir sua senha. Clique no botão abaixo para criar uma nova senha:</p>
                <p>
                    <a href="{$url_recuperacao}" class="button">Redefinir Senha</a>
                </p>
                <p>Ou acesse o link: <a href="{$url_recuperacao}">{$url_recuperacao}</a></p>
                <p>Este link expirará em 1 hora.</p>
                <p>Se você não solicitou a redefinição de senha, ignore este e-mail.</p>
                <p>Atenciosamente,<br>Equipe Sistema Gestão de Manutenção</p>
            </div>
            <div class="footer">
                <p>Este é um e-mail automático, por favor não responda.</p>
            </div>
        </div>
    </body>
    </html>
    HTML;

    return $html;
}
