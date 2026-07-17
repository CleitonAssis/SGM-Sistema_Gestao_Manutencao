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

if (empty($token) || strlen($token) !== 32 || !ctype_xdigit($token)) {
    echo json_encode(['status' => false, 'mensagem' => 'Token não informado ou inválido.']);
    exit();
}

//$token = strtolower($token);


// Tenta validar a conta
$resultado = validarConta($token);

// Retorna o resultado
echo json_encode($resultado);
?>