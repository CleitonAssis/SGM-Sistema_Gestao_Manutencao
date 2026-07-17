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

$stmt = $conn->prepare("SELECT nome, data_hora_expiracao_token FROM usuarios WHERE token_recuperacao = ?");
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

// Data do banco (YYYY-MM-DD HH:MM:SS)
$dataExpiracao = new DateTime($dados['data_hora_expiracao_token']);
$agora = new DateTime();

// Verifica se expirou
$tokenExpirado = $dataExpiracao < $agora;

// Mensagem para o usuário
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGM-Redefinir Senha</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/sgm/app/assets/bootstrap-4.6.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/sgm/app/assets/font-awesome-7.1.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/sgm/app/assets/sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="/sgm/app/views/css/redefinir_senha.css">
</head>

<body>
    <div class="container">
        <div class="password-container">
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
            <h3 class="text-center font-weight-bold">Redefinir Senha</h3>
            <div class="form-text text-center">
                <?php echo $msg; ?>
            </div>
            <?php if (!$tokenExpirado): ?>
            <form class="needs-validation mt-3" id="formRedefinirSenha" novalidate>
                <div class="form-group position-relative">
                    <label for="nova_senha" class="form-label">Nova Senha</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="nova_senha" name="nova_senha" placeholder="Digite sua nova senha" required minlength="6" maxlength="15">
                        <span class="toggle-password senha" data-target="#senha">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                        <div class="invalid-feedback">A senha deve ter no mínimo 7 caracteres.</div>
                    </div>
                </div>

                <div class="form-group position-relative">
                    <label for="confirmar_nova_senha" class="form-label">Confirmar Nova Senha</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="confirmar_nova_senha" name="confirmar_nova_senha" placeholder="Confirme sua nova senha" required minlength="6" maxlength="15">
                        <span class="toggle-password confirmar_nova_senha" data-target="#confirmar_nova_senha">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                        <div class="invalid-feedback">As senhas não coincidem.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-info btn-block">
                    Redefinir Senha
                </button>
            </form>
            <?php endif; ?>
            <div class="form-text text-center mt-3 link-tema">
                <a href="./login">Ir para Login</a>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="/sgm/app/assets/jquery-3.6.0/jquery.min.js"></script>
    <!-- jQuery Mask Plugin -->
    <script src="/sgm/app/assets/jquery-mask/jquery.mask.js"></script>
    <!-- Bootstrap JS -->
    <script src="/sgm/app/assets/bootstrap-4.6.2/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="/sgm/app/assets/sweetalert2/sweetalert2.min.js"></script>
    <!-- Script personalizado -->
    <script src="/sgm/app/views/js/redefinir_senha.js"></script>
    <script>
        $(document).ready(function() {
            // Mostrar/ocultar senha
            $('.toggle-password').click(function() {
                const passwordField = $(this).closest('.input-group').find('input');
                const passwordFieldType = passwordField.attr('type');
                const newType = passwordFieldType === 'password' ? 'text' : 'password';
                passwordField.attr('type', newType);

                // Alterna o ícone
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            // Validação do formulário
            $('#formRedefinirSenha').submit(function(e) {
                e.preventDefault();

                // Pega o token da URL
                const urlParams = new URLSearchParams(window.location.search);
                const token = urlParams.get('token');
                //console.log(token);

                const novaSenha = $('#nova_senha').val().trim();
                const confirmarNovaSenha = $('#confirmar_nova_senha').val().trim();

                // Verifica se o token está presente
                if (!token) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Token inválido',
                        text: 'Token de redefinição de senha não encontrado!'
                    });
                    return;
                }

                if (!novaSenha || !confirmarNovaSenha) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção',
                        text: 'Preencha todos os campos.'
                    });
                    return;
                }

                // Valida formulário
                if (!validarFormulario()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Corrija os campos destacados em vermelho.'
                    });
                    return;
                }

                // Envia os dados para o servidor via AJAX
                $.ajax({
                    url: '/sgm/app/controllers/processar_redefinir_senha.php',
                    type: 'POST',
                    data: {
                        token: token,
                        nova_senha: novaSenha,
                        confirmar_nova_senha: confirmarNovaSenha
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
                                confirmButtonText: 'OK'
                            }).then(function() {
                                // Limpa os campos do formulário
                                $('#formRedefinirSenha')[0].reset();
                                window.location.href = './login';
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
                        $('#formRedefinirSenha')[0].reset();
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Ocorreu um erro ao processar a solicitação. Tente novamente mais tarde.'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>