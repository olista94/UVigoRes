function isValidDni(dni) {
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

function isValidEmail(email) {
    var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    return pattern.test(email);
}

function isValidNiu(niu) {
    const regex = /^0050000\d{5}$/;
    return regex.test(niu);
}

function isValidForm() {
    const dni = document.getElementById('dni').value;
    const email = document.getElementById('email').value;
    const niu = document.getElementById('niu').value;
    const password = document.getElementById('contrasena').value;

    if (!isValidDni(dni)) {
        showErrorMessage('DNI no válido');
        return false;
    }

    if (!isValidEmail(email)) {
        showErrorMessage('Correo electrónico no válido');
        return false;
    }

    if (!isValidNiu(niu)) {
        showErrorMessage('El Niu no es válido');
        return false;
    }

    if (!isValidPassword(password)) {
        showErrorMessage('La contrasena no es válida');
        return false;
    }

    return true;
}

function showErrorMessage(msg) {
    const messageDiv  = document.getElementById('validation-message');
    const messageElem = document.createElement('p');
    
    messageElem.innerText = msg;
    messageDiv.appendChild(messageElem);
    messageDiv.classList.toggle('hidden');
 
    setTimeout(() => {
        messageDiv.removeChild(messageElem);
        messageDiv.classList.toggle('hidden');
    }, 3000)
}

document.getElementById('submit-btn').addEventListener('click', (event) => {
    event.preventDefault();

    if (!isValidForm()) {
        return;
    }

    document.querySelector('form').submit();
});
