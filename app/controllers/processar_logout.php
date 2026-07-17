<?php
require_once( dirname(__DIR__) . '/models/funcoes_usuario.php');

// Define o cabeçalho como JSON
header('Content-Type: application/json');

// Chama a função de logout
if (logoutUsuario()) {
    echo json_encode(['status' => true, 'mensagem' => 'Logout realizado com sucesso']);
} else {
    echo json_encode(['status' => false, 'mensagem' => 'Erro ao realizar logout']);
}
exit;
?>