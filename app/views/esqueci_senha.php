<?php
session_start();

// Se o usuário já estiver logado, redireciona para a página principal
if (isset($_SESSION['usuario_id'])) {
    header('Location: ./painel');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGM-Esqueci a Senha</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/sgm/app/assets/bootstrap-4.6.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/sgm/app/assets/font-awesome-7.1.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/sgm/app/assets/sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="/sgm/app/views/css/esqueci_senha.css">
</head>

<body>
    <div class="container">
        <div class="esqueci-senha-container">
            <h3 class="text-center font-weight-bold mb-4">Recuperação de Senha</h3>
            <form class="needs-validation" id="formEsqueciSenha" novalidate>
                <div class="form-group">
                    <label for="cpf" class="form-label">CPF</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                        </div>
                        <input type="text" class="form-control" id="cpf" name="cpf" placeholder="Insira o CPF cadastrado">
                        <div class="invalid-feedback">Informe um CPF válido.</div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">E-mail</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Insira o e-mail cadastrado">
                        <div class="invalid-feedback">Informe um e-mail válido.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-info btn-block">
                    Recuperar
                </button>

                <div class="form-text text-center mt-3 link-tema">
                    <a href="./login">Voltar para Login</a>
                </div>
            </form>
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
    <script src="/sgm/app/views/js/esqueci_senha.js"></script>
    <script>
        $(document).ready(function() {
            // Máscara de CPF
            $('#cpf').mask('000.000.000-00', {
                reverse: true
            });

            // Validação do formulário
            $('#formEsqueciSenha').submit(function(e) {
                e.preventDefault();

                const cpf = $('#cpf').val().trim();
                const email = $('#email').val().trim();

                if (!cpf || !email) {
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

                // Envia os dados para o servidor via AJAX
                $.ajax({
                    url: '/sgm/app/controllers/processar_esqueci_senha.php',
                    type: 'POST',
                    data: {
                        cpf: cpf,
                        email: email
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
                                $('#formEsqueciSenha')[0].reset();
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
                        $('#formEsqueciSenha')[0].reset();
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