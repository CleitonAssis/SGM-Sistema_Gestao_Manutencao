// Validação do formulário
function validarFormulario() {
    let formValido = true;

    // Validar senha
    if ($('#nova_senha').val().length < 7) {
        $('#nova_senha').addClass('is-invalid');
        formValido = false;
    } else {
        $('#nova_senha').removeClass('is-invalid');
        $('#nova_senha').addClass('is-valid');
    }

    // Validar confirmação de senha
    if ($('#nova_senha').val() !== $('#confirmar_nova_senha').val()) {
        $('#confirmar_nova_senha').addClass('is-invalid');
        formValido = false;
    } else {
        $('#confirmar_nova_senha').removeClass('is-invalid');
        $('#confirmar_nova_senha').addClass('is-valid');
    }

    return formValido;
}