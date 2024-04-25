const mainForm = document.forms[0];

async function apiGet(requestType, value)
{
    value = encodeURIComponent(value);

    const baseURL = "/app/api/form_validation.php";
    const requestURL = `${baseURL}?request=${requestType}&value=${value}`;

    const request = await fetch(requestURL);
    return request.text();
}

async function validateInput(input)
{
    const errorMsgElement = mainForm.getElementsByClassName("form-error-msg")[0];

    if (input.name === "email" && input.value !== input.defaultValue) {
        const validationMessage = await apiGet("isUserEmailTaken", input.value);
        input.setCustomValidity(validationMessage);
    }
    elseif (input.name === "username" && input.value !== input.defaultValue) {
        const validationMessage = await apiGet("isUsernameTaken", input.value);
        input.setCustomValidity(validationMessage);
    }

    if (! input.checkValidity() && input.value) {
        if (input.validity.customError)
            errorMsgElement.innerText = input.validationMessage;
        else
            errorMsgElement.innerText = input.title;
    }
    else {
        input.setCustomValidity("");
        errorMsgElement.innerText = "";
    }
}

for (const input of mainForm.elements) {
    input.addEventListener("keyup", () => {
        setTimeout(async() => validateInput(input), 200);
    });
    input.addEventListener("focus", async() =>
        validateInput(input)
    );
    input.addEventListener("blur", () => {
        mainForm.getElementsByClassName("form-error-msg")[0].innerText = "";
    });
}
