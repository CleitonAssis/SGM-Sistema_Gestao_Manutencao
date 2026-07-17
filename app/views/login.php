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
session_start();

// Se o usuário já estiver logado, redireciona para a página principal
if (isset($_SESSION['usuario_id'])) {
    header('Location: ./painel');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Gestão de Manutenção</title>

    <!-- Meta tags Open Graph para Facebook, WhatsApp e redes sociais -->
    <meta property="og:title" content="Sistema Gestão de Manutenção">
    <meta property="og:description" content="Página de login do sistema.">
    <meta property="og:image" content="https://prefeitura.belooriente.mg.gov.br/sgm/app/img/logo.png"> <!-- Substitua pelo caminho da sua logo -->
    <meta property="og:url" content="https://prefeitura.belooriente.mg.gov.br/sgm"> <!-- Substitua pelo seu URL -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Sistema Gestão de Manutenção">

    <!-- Meta tags Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema Gestão de Manutenção">
    <meta name="twitter:description" content="Página de login do sistema.">
    <meta name="twitter:image" content="https://prefeitura.belooriente.mg.gov.br/sgm/app/img/logo.png"> <!-- Substitua pelo caminho da sua logo -->


    <!-- Bootstrap -->
    <link rel="stylesheet" href="/sgm/app/assets/bootstrap-4.6.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/sgm/app/assets/font-awesome-7.1.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/sgm/app/assets/sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="/sgm/app/views/css/login.css">
</head>

<body class="bg-light">

    <div class="container">
        <div class="login-container">
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

            <form class="needs-validation" id="loginForm" novalidate>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                        </div>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Insira o e-mail" required>
                        <div class="invalid-feedback">Informe um e-mail válido.</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="Insira a senha" required>
                        <span class="toggle-password" title="Mostrar/Ocultar senha">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                        <div class="invalid-feedback">A senha deve ter no mínimo 7 caracteres.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-info btn-block">
                    Entrar
                </button>

                <div class="form-text text-center link-tema mt-3">
                    <div>
                        Não tem uma conta?
                        <a href="./cadastro">Cadastre-se</a>
                    </div>

                    <div>
                        Esqueceu a senha?
                        <a href="./esqueci_senha">Clique aqui</a>
                    </div>
                </div>
            </form>

            <hr>

            <div class="text-center link-tema">
                <small>
                    Desenvolvido por
                    <a href="https://www.cvacentertech.com.br" target="_blank" rel="noopener noreferrer">
                        Cleiton Assis
                    </a> &copy; <?php echo date('Y'); ?>
                </small>
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
    <script src="/sgm/app/views/js/login.js"></script>
    <script>
        $(document).ready(function() {
            // Mostrar/ocultar senha
            $('.toggle-password').on('click', function() {
                const input = $('#senha');
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#loginForm').submit(function(e) {
                e.preventDefault();

                const email = $('#email').val().trim();
                const senha = $('#senha').val().trim();

                if (!email || !senha) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção',
                        text: 'Preencha todos os campos.'
                    });
                    return;
                }

                if (!validarFormulario()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Corrija os campos destacados em vermelho.'
                    });
                    return;
                }

                $.ajax({
                    url: '/sgm/app/controllers/processar_login.php',
                    type: 'POST',
                    data: {
                        email: email,
                        senha: senha
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Processando...',
                            html: 'Por favor, aguarde.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso',
                                text: response.mensagem,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Limpa os campos do formulário
                                $('#loginForm')[0].reset();
                                window.location.href = './painel';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error', 
                                title: 'Erro', 
                                text: response.mensagem
                            }).then(function() {
                                // Recarrega a página
                                window.location.reload();
                            });
                        }
                    },
                    error: function() {
                        // Limpa os campos do formulário
                        $('#loginForm')[0].reset();
                        Swal.fire(
                            'Erro',
                            'Erro ao processar a solicitação.',
                            'error'
                        );
                    }
                });
            });

        });
    </script>

</body>

</html>