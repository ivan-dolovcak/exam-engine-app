const passwordInput = document.getElementById("password");

document.getElementById("btn-password-peak").addEventListener(
    "mousedown", () => passwordInput.type = "text");
document.getElementById("btn-password-peak").addEventListener(
    "mouseup", () => passwordInput.type = "password");
document.getElementById("btn-password-peak").addEventListener(
    "click", () => passwordInput.type
        = passwordInput.type === "password" ? "text" : "password");
