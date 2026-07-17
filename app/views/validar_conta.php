<?php
// Se não houver sessão ativa, inicia uma nova sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se o usuário já estiver logado, redireciona para a página principal
if (isset($_SESSION['usuario_id'])) {
    header('Location: ./painel');
    exit();
}

require_once(dirname(__DIR__) . '/models/funcoes_usuario.php');

if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: ./login");
    exit();
}

$token = $_GET['token'];
$conn = conectarBD();

$stmt = $conn->prepare("SELECT nome, data_hora_expiracao_token FROM usuarios WHERE token_validacao = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: ./login");
    exit();
}

$dados = $result->fetch_assoc();
$stmt->close();
$conn->close();

$nome = $dados['nome'];
$primeiroNome = explode(' ', trim($nome))[0];

// Datas
$dataExpiracao = new DateTime($dados['data_hora_expiracao_token']);
$agora = new DateTime();

// Verifica se expirou
$tokenExpirado = $dataExpiracao < $agora;

// Mensagem
if ($tokenExpirado) {
    $msg = "<button class='btn btn-danger btn-lg mt-3' disabled><i class='fa-solid fa-circle-xmark'></i> Link expirado!</button>";
} else {
    $msg = sprintf("<span class='text-success'>%s, seu link expira em %s.</span>", htmlspecialchars(ucfirst($primeiroNome)), $dataExpiracao->format('d/m/Y H:i'));
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>SGM-Validação de Conta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="/sgm/app/assets/bootstrap-4.6.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/sgm/app/assets/font-awesome-7.1.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/sgm/app/assets/sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="/sgm/app/views/css/validar_conta.css">
</head>

<body class="bg-light">
    <div class="container">
        <div class="validate-container">
            <p class="text-center link-tema" title="Sistema Gestão de Manutenção">

                <i class="fa-solid fa-computer text-info system-icon"></i>
                <!-- Iniciais SGM -->
                <span class="system-initials"><a class="font-weight-bold" href="#" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">SGM</a></span>
                <div class="collapse" id="collapse">
                    <div class="card card-body text-center font-weight-bold">
                        Sistema Gestão de Manutenção.
                    </div>
                </div>
            </p>
            <h3 class="text-center font-weight-bold">Validação de Conta</h3>
            <div class="form-text text-center">
                <?php echo $msg; ?>
            </div>
            <div class="form-text text-center mt-3">
                <?php if (!$tokenExpirado): ?>
                    <button id="btnValidarConta" class="btn btn-info btn-lg">
                        <i class="fa-solid fa-check-circle"></i> Validar Conta
                    </button>
                <?php endif; ?>
            </div>

            <div class="form-text text-center mt-3 link-tema">
                <a class="text-decoration-none" href="./login">Ir para Login</a>
            </div>
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
        $(document).ready(function() {
            $('#btnValidarConta').click(function() {
                const urlParams = new URLSearchParams(window.location.search);
                const token = urlParams.get('token');

                if (!token) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Token inválido',
                        text: 'Token de validação não encontrado.'
                    });
                    return;
                }

                $.ajax({
                    url: '/sgm/app/controllers/processar_validar_conta.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        token: token
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Validando...',
                            html: 'Por favor, aguarde.',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Conta validada com sucesso',
                                text: response.mensagem,
                                confirmButtonText: 'Ir para Login'
                            }).then(() => {
                                // Limpar o token da URL e redirecionar para login
                                const url = new URL(window.location.href);
                                url.searchParams.delete('token');
                                window.history.replaceState({}, document.title, url);
                                window.location.href = './login';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.mensagem
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao processar a solicitação. Tente novamente.'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>