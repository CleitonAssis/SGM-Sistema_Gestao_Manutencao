<?php
// Inclui o arquivo de configuração do banco de dados
require_once (__DIR__ . '/app/config/database.php');

// Tenta estabelecer a conexão
try {
    $conn = conectarBD();
    $conn->close();
    header("Location: /sgm/login");
    exit;
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}
?>
