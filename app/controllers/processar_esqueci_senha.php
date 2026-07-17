<?php
header('Content-Type: application/json');
require_once(dirname(__DIR__) . '/models/funcoes_usuario.php');

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'mensagem' => 'Método de requisição inválido.']);
    exit();
}

// Obtém o e-mail do formulário
$cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Valida o campo de e-mail
if (empty($email) || empty($cpf)) {
    echo json_encode(['status' => false, 'mensagem' => 'Por favor, preencha todos os campos obrigatórios.']);
    exit();
}

// Valida o campo de CPF
if (!validarCPF($cpf)) {
    echo json_encode(['status' => false, 'mensagem' => 'Por favor, informe um CPF válido.']);
    exit();
}

// Valida o domínio do e-mail
if (!validarDominioEmail($email)) {
    echo json_encode(['status' => false, 'mensagem' => 'Domínio de e-mail inválido.']);
    exit();
}

// Valida o formato do e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => false, 'mensagem' => 'Por favor, informe um e-mail válido.']);
    exit();
}

// Tenta recuperar a senha
$resultado = recuperarSenha($cpf, $email);

// Retorna o resultado
echo json_encode($resultado);
?>