function isValidType(type) {
    var pattern = /^[^0-9]+$/;
    return pattern.test(type);
}

function isValidForm() {
    const type = document.getElementById('Tipo').value;


    if (!isValidType(type)) {
        showErrorMessage('Tipo no válido');
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
