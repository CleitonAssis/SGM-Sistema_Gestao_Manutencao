<?php
session_start();
header('Content-Type: application/json');
require_once(dirname(__DIR__) . '/models/funcoes_usuario.php');

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'mensagem' => 'Método de requisição inválido.']);
    exit();
}

// Obtém os dados do formulário
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['senha']) ? $_POST['senha'] : '';

// Valida os campos obrigatórios
if (empty($email) || empty($senha)) {
    echo json_encode(['status' => false, 'mensagem' => 'Por favor, preencha todos os campos.']);
    exit();
}

// Valida o formato do e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => false, 'mensagem' => 'Por favor, informe um e-mail válido.']);
    exit();
}

//sleep(5); // pausa a execução por 5 segundos

// Tenta realizar o login
$resultado = loginUsuario($email, $senha);

// Se o login for bem-sucedido, cria a sessão do usuário
if ($resultado['status']) {
    $_SESSION['usuario_id'] = $resultado['usuario']['id'];
    $_SESSION['usuario_nome'] = $resultado['usuario']['nome'];
    $_SESSION['usuario_telefone'] = $resultado['usuario']['telefone'];
    $_SESSION['usuario_email'] = $resultado['usuario']['email'];
    $_SESSION['usuario_validado'] = $resultado['usuario']['validado'];
}

// Retorna o resultado
echo json_encode($resultado);
?>