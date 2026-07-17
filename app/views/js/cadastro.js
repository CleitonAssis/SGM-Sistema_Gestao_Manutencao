// Funções de validação de formulário

// Validação do CPF
function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');

    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;

    let soma = 0;
    let resto;

    for (let i = 1; i <= 9; i++) {
        soma = soma + parseInt(cpf.substring(i - 1, i)) * (11 - i);
    }

    resto = (soma * 10) % 11;

    if ((resto === 10) || (resto === 11)) resto = 0;
    if (resto !== parseInt(cpf.substring(9, 10))) return false;

    soma = 0;

    for (let i = 1; i <= 10; i++) {
        soma = soma + parseInt(cpf.substring(i - 1, i)) * (12 - i);
    }

    resto = (soma * 10) % 11;

    if ((resto === 10) || (resto === 11)) resto = 0;
    if (resto !== parseInt(cpf.substring(10, 11))) return false;

    return true;
}

// Validação do e-mail
function validarEmail(email) {
    const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(email);
}

// Validação do formulário
function validarFormulario() {
    let formValido = true;

    // Validar nome (mínimo duas palavras)
    var nome = $('#nome').val().trim();

    if (nome === '' || nome.split(/\s+/).length < 2) {
        $('#nome').addClass('is-invalid');
        $('#nome').removeClass('is-valid');
        formValido = false;
    } else {
        $('#nome').removeClass('is-invalid');
        $('#nome').addClass('is-valid');
    }


    // Validar CPF
    if (!validarCPF($('#cpf').val())) {
        $('#cpf').addClass('is-invalid');
        formValido = false;
    } else {
        $('#cpf').removeClass('is-invalid');
        $('#cpf').addClass('is-valid');
    }

    // Validar telefone
    const telefone = $('#telefone').val().replace(/[^\d]+/g, '');
    if (telefone.length < 10) {
        $('#telefone').addClass('is-invalid');
        formValido = false;
    } else {
        $('#telefone').removeClass('is-invalid');
        $('#telefone').addClass('is-valid');
    }

    // Validar e-mail
    if (!validarEmail($('#email').val())) {
        $('#email').addClass('is-invalid');
        formValido = false;
    } else {
        $('#email').removeClass('is-invalid');
        $('#email').addClass('is-valid');
    }

    // Validar senha
    if ($('#senha').val().length < 7) {
        $('#senha').addClass('is-invalid');
        formValido = false;
    } else {
        $('#senha').removeClass('is-invalid');
        $('#senha').addClass('is-valid');
    }

    // Validar confirmação de senha
    if ($('#senha').val() !== $('#confirmar_senha').val()) {
        $('#confirmar_senha').addClass('is-invalid');
        formValido = false;
    } else {
        $('#confirmar_senha').removeClass('is-invalid');
        $('#confirmar_senha').addClass('is-valid');
    }

    return formValido;
}