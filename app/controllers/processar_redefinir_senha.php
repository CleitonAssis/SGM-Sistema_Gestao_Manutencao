<?php
header('Content-Type: application/json');
require_once(dirname(__DIR__) . '/models/funcoes_usuario.php');

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'mensagem' => 'Método de requisição inválido.']);
    exit();
}

// Obtém os dados do formulário
$token = isset($_POST['token']) ? trim($_POST['token']) : '';
$nova_senha = isset($_POST['nova_senha']) ? trim($_POST['nova_senha']) : '';
$confirmar_nova_senha = isset($_POST['confirmar_nova_senha']) ? trim($_POST['confirmar_nova_senha']) : '';

// Valida o token
if (empty($token) || strlen($token) !== 32 || !ctype_xdigit($token)) {
    echo json_encode(['status' => false, 'mensagem' => 'Token não informado ou inválido.']);
    exit();
}

// Valida os campos
if (empty($nova_senha) || empty($confirmar_nova_senha)) {
    echo json_encode(['status' => false, 'mensagem' => 'Por favor, preencha todos os campos obrigatórios.']);
    exit();
}

// Validar a nova senha (mínimo 7 caracteres)
if (strlen($nova_senha) < 7) {
    echo json_encode(['status' => false, 'mensagem' => 'A nova senha deve ter pelo menos 7 caracteres.']);
    exit();
}

// Verifica se as senhas coincidem
if ($nova_senha !== $confirmar_nova_senha) {
    echo json_encode(['status' => false, 'mensagem' => 'As senhas não coincidem.']);
    exit();
}

// Criptografa a nova senha
$nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

// Tenta redefinir a senha
$resultado = redefinirSenha($token, $nova_senha_hash);

// Retorna o resultado
echo json_encode($resultado);
?>