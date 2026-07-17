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
    <title>SGM-Cadastro de Usuário</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/sgm/app/assets/bootstrap-4.6.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/sgm/app/assets/font-awesome-7.1.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/sgm/app/assets/sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="/sgm/app/views/css/cadastro.css">
</head>

<body>
    <div class="container">
        <div class="cadastro-container">

            <h3 class="text-center font-weight-bold mb-4">Cadastro de Usuário</h3>

            <form class="needs-validation" id="cadastroForm" novalidate>

                <!-- Nome -->
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="nome" placeholder="Digite seu nome completo" required>
                        <div class="invalid-feedback">Seu nome deve conter pelo menos 2 palavras.</div>
                    </div>
                </div>

                <!-- CPF -->
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                        </div>
                        <input type="text" class="form-control" id="cpf" placeholder="Digite apenas os números do CPF" required>
                        <div class="invalid-feedback">Informe um CPF válido.</div>
                    </div>
                </div>

                <!-- Telefone -->
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                        </div>
                        <input type="text" class="form-control" id="telefone" placeholder="Digite apenas os números do telefone" required>
                        <div class="invalid-feedback">Informe um telefone válido.</div>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" id="email" placeholder="Digite seu e-mail" required>
                        <div class="invalid-feedback">Informe um e-mail válido.</div>
                    </div>
                </div>

                <!-- Senha -->
                <div class="form-group position-relative">
                    <label for="senha">Senha</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        </div>

                        <input type="password" class="form-control" id="senha" placeholder="Mínimo de 7 caracteres" required>
                        <span class="toggle-password senha" data-target="#senha">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                        <div class="invalid-feedback">A senha deve ter no mínimo 7 caracteres.</div>
                    </div>
                </div>

                <!-- Confirmar Senha -->
                <div class="form-group position-relative">
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        </div>

                        <input type="password" class="form-control" id="confirmar_senha" placeholder="Confirme sua senha" required>
                        <span class="toggle-password confirmar-senha" data-target="#confirmar_senha">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                        <div class="invalid-feedback">As senhas não coincidem.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-info btn-block">
                    Cadastrar
                </button>

                <div class="form-text text-center mt-3 link-tema">
                    Já tem conta? <a href="./login">Faça login</a>
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
    <script src="/sgm/app/views/js/cadastro.js"></script>
    <script>
        $(document).ready(function() {
            // Aplicando máscaras
            $('#cpf').mask('000.000.000-00', {
                reverse: true
            });
            $('#telefone').mask('(00) 00000-0000');
            $('#telefone').on('keyup', function() {
                var value = $(this).val().replace(/\D/g, '');
                if (value.length > 10) {
                    $(this).mask('(00) 00000-0000');
                } else {
                    $(this).mask('(00) 0000-00009');
                }
            });

            // Mostrar/ocultar senha
            $('.senha').on('click', function() {
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

            $('.confirmar-senha').on('click', function() {
                const input = $('#confirmar_senha');
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Submissão do formulário de cadastro
            $('#cadastroForm').submit(function(e) {
                e.preventDefault();

                const nome = $('#nome').val().trim();
                const cpf = $('#cpf').val().trim();
                const telefone = $('#telefone').val().trim();
                const email = $('#email').val().trim();
                const senha = $('#senha').val().trim();
                const confirmar_senha = $('#confirmar_senha').val().trim();

                if (!nome || !cpf || !telefone || !email || !senha || !confirmar_senha) {
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
                    url: '/sgm/app/controllers/processar_cadastro.php',
                    type: 'POST',
                    data: {
                        nome: nome,
                        cpf: cpf,
                        telefone: telefone,
                        email: email,
                        senha: senha,
                        confirmar_senha: confirmar_senha
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
                                $('#cadastroForm')[0].reset();
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
                        $('#cadastroForm')[0].reset();
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