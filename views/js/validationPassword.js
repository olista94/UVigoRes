function isValidPassword(contrasena) {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,128}$/;
    return regex.test(contrasena);
}

function doPasswordsMatch(password1, password2) {
    return password1 === password2;
}

function isValidForm() {
    const password = document.getElementById('Nueva_Contrasena').value;
    const confirmPassword = document.getElementById('Confirmar_Contrasena').value;

    if (!isValidPassword(password)) {
        showErrorMessage('La contraseña no es válida');
        return false;
    }

    if (!doPasswordsMatch(password, confirmPassword)) {
        showErrorMessage('Las contraseñas no coinciden');
        return false;
    }

    return true;
}

function showErrorMessage(msg) {
    const messageDiv = document.getElementById('validation-message');
    const messageElem = document.createElement('p');

    messageElem.innerText = msg;
    messageDiv.appendChild(messageElem);
    messageDiv.classList.toggle('hidden');

    setTimeout(() => {
        messageDiv.removeChild(messageElem);
        messageDiv.classList.toggle('hidden');
    }, 3000);
}

document.getElementById('submit-btn').addEventListener('click', (event) => {
    event.preventDefault();

    if (!isValidForm()) {
        return;
    }

    document.querySelector('form').submit();
});
