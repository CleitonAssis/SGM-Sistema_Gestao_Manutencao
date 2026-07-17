<?php
// Configurações de conexão com o banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'cvact');
define('DB_PASS', 'cvact@#$123');
define('DB_NAME', 'sgm_db');

// Configura o mysqli para relatar erros e exceções
mysqli_report(MYSQLI_REPORT_ERROR);

function conectarBD() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        throw new Exception("Erro de conexão com o banco de dados: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
    return $conn;
}

?>
