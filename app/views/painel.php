<?php
require_once(dirname(__DIR__) . '/config/database.php');
// Tenta estabelecer a conexão
try {
    $conn = conectarBD();
    $conn->close();
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

// Se não houver sessão ativa, inicia uma nova sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário realizou a validação da conta
if (empty($_SESSION['usuario_validado']) || $_SESSION['usuario_validado'] !== 1) {
    header('Location: ./login');
    exit;
}

// Dados do usuário
$usuario_id    = $_SESSION['usuario_id'] ?? '';
$usuario_nome  = $_SESSION['usuario_nome'] ?? '';
$usuario_telefone = $_SESSION['usuario_telefone'] ?? '';
$usuario_email = $_SESSION['usuario_email'] ?? '';
$primeiroNome = explode(' ', trim($usuario_nome))[0];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SGM-Painel do Usuário</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="/sgm/app/assets/bootstrap-4.6.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/sgm/app/assets/font-awesome-7.1.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/sgm/app/assets/sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="/sgm/app/views/css/custom-navbar.css">
    <link rel="stylesheet" href="/sgm/app/views/css/painel.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-cyan">
        <div class="container">
            <a class="navbar-brand font-weight-bold" href="./painel">
                <i class="fa-solid fa-computer"></i>
                SGM
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['painel.php']) ? 'active' : ''; ?>" href="./painel">
                            <i class="fa-solid fa-home"></i>
                            Início
                        </a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link <?php //echo in_array(basename($_SERVER['PHP_SELF']), ['equipamentos.php']) ? 'active' : ''; ?>" href="./equipamentos">
                            <i class="fa-solid fa-building-columns"></i>
                            Setores
                        </a>
                    </li>-->
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="btnLogout">
                            <i class="fa-solid fa-sign-out-alt"></i>
                            Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo -->
    <div class="container">
        <div class="painel-container">

            <div class="mb-4">
                <h2>Bem-vindo, <?= htmlspecialchars(ucfirst($primeiroNome)) ?>!</h2>
                <p class="text-muted">Você está logado no sistema.</p>
            </div>

            <div class="user-info">
                <h5 class="mb-3">Suas Informações</h5>
                <p><strong>ID:</strong> <?= htmlspecialchars($usuario_id) ?></p>
                <p><strong>Nome:</strong> <?= htmlspecialchars(ucwords($usuario_nome)) ?></p>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($usuario_telefone) ?></p>
                <p><strong>E-mail:</strong> <?= htmlspecialchars($usuario_email) ?></p>
            </div>

            <!--<button class="btn btn-danger btn-block" id="btnLogoutConfirm">
                <i class="fa-solid fa-sign-out-alt"></i> Sair do Sistema
            </button>-->

        </div>
    </div>

    <!-- jQuery -->
    <script src="/sgm/app/assets/jquery-3.6.0/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="/sgm/app/assets/bootstrap-4.6.2/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="/sgm/app/assets/sweetalert2/sweetalert2.min.js"></script>
    <!-- Script personalizado -->
    <script>
        $(function() {
            // Função para realizar logout
            function realizarLogout() {
                $.ajax({
                        url: '/sgm/app/controllers/processar_logout.php',
                        type: 'GET',
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Saindo...',
                                html: 'Por favor, aguarde.',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                        }
                    })
                    .done(function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso',
                                text: response.mensagem || 'Logout realizado com sucesso.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = './login';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.mensagem || 'Erro ao sair do sistema.'
                            });
                        }
                    })
                    .fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Não foi possível processar o logout.'
                        });
                    });
            }

            // Função para confirmar logout
            function confirmarLogout() {
                Swal.fire({
                    title: 'Deseja realmente sair?',
                    text: 'Você será desconectado do sistema.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, sair',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        realizarLogout();
                    }
                });
            }

            // Eventos de clique para logout
            $('#btnLogout, #btnLogoutConfirm').on('click', function(e) {
                e.preventDefault();
                confirmarLogout();
            });

        });
    </script>

</body>

</html>