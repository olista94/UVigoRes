function isValidName(name) {
    var pattern = /^[a-zA-Z]+$/;
    return pattern.test(name);
}

function isValidAddress(addres) {
    var pattern = /[A-Za-zÀ-ÖØ-öø-ÿ0-9ºª.,/\s-]+/;
    return pattern.test(addres);
}

function isValidPhone(phone) {
    const regex = /^(?:\+34)?[679]\d{8}$/;
    return regex.test(phone);
}

function isValidEmail(email) {
    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    return regex.test(email);
}

function isValidForm() {
    const name = document.getElementById('Nombre').value;
    const addres = document.getElementById('Direccion').value;
    const phone = document.getElementById('Telefono').value;
    const email = document.getElementById('Email').value;

    if (!isValidName(name)) {
        showErrorMessage('Nombre no válido');
        return false;
    }

    if (!isValidAddress(addres)) {
        showErrorMessage('Dirección no válida');
        return false;
    }

    if (!isValidPhone(phone)) {
        showErrorMessage('Teléfono no válido');
        return false;
    }

    if (!isValidEmail(email)) {
        showErrorMessage('Correo electrónico no válido');
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
