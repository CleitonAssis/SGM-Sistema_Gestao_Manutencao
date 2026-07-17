// Funções de validação de formulário

// Validação do e-mail
function validarEmail(email) {
    const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(email);
}

// Validação do formulário
function validarFormulario() {
    let formValido = true;

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

    return formValido;
}