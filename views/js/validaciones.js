function validarDNI(dni) {
    var pattern = /^[0-9]{8}[A-Za-z]$/;
    if (!pattern.test(dni)) {
        return false;
    }
    var numero = dni.substr(0, dni.length - 1);
    var letra = dni.substr(dni.length - 1, 1);
    var letras = 'TRWAGMYFPDXBNJZSQVHLCKET';
    if (letra.toUpperCase() !== letras.charAt(numero % 23)) {
        return false;
    }
    return true;
}

function validarEmail(email) {
    var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    return pattern.test(email);
}

function validarFormulario() {
    var dni = document.getElementById('dni').value;
    var email = document.getElementById('email').value;

    if (!validarDNI(dni)) {
        alert('DNI no válido');
        return false;
    }

    if (!validarEmail(email)) {
        alert('Correo electrónico no válido');
        return false;
    }

    return true;
}

document.getElementById('miFormulario').onsubmit = function() {
    return validarFormulario();
};
