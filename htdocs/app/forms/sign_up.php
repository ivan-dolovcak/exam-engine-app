<?php
require_once "config.php";
session_start();

$successPage = "/views/index.phtml";
$failurePage = "/views/sign_up.phtml";

if ($_SERVER["REQUEST_METHOD"] !== "POST"
    || ! isset($_POST["username"], $_POST["email"], $_POST["password"],
        $_POST["firstName"],$_POST["lastName"]))
{
    $_SESSION["formErrorMsg"] = LANG["invalidPost"];
    header("Location: $failurePage");
    die;
}

$username   = Util::sanitizeFormData($_POST["username"]);
$email      = Util::sanitizeFormData($_POST["email"]);
$password   = Util::sanitizeFormData($_POST["password"]);
$firstName  = Util::sanitizeFormData($_POST["firstName"]);
$lastName   = Util::sanitizeFormData($_POST["lastName"]);

if (! preg_match(UserModel::REGEX_VALID_USERNAME, $username)) {
    $_SESSION["formErrorMsg"] = LANG["invalidUsername"];
}
elseif (! preg_match(UserModel::REGEX_VALID_NAME, $firstName)
    || ! preg_match(UserModel::REGEX_VALID_NAME, $lastName))
{
    $_SESSION["formErrorMsg"] = LANG["invalidName"];
}
elseif (! preg_match(UserModel::REGEX_VALID_PASSWORD, $password)) {
    $_SESSION["formErrorMsg"] = LANG["invalidPassword"];
}
elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)
    || ! checkdnsrr(substr($email, strpos($email, "@")+1)))
{
    $_SESSION["formErrorMsg"] = LANG["invalidEmail"];
}
else {
    $user = UserModel::ctorSignUp(
        $username, $email, $password, $firstName, $lastName);

    $errorMsg = $user->signUp();
    if (isset($errorMsg)) {
        if (str_contains($errorMsg, "UK_username"))
            $_SESSION["formErrorMsg"] = LANG["usernameTakenError"];
        elseif (str_contains($errorMsg, "UK_email"))
            $_SESSION["formErrorMsg"] = LANG["emailTakenError"];
        else
            $_SESSION["formErrorMsg"] = LANG["dbError"];
    }
}

if (isset($_SESSION["formErrorMsg"]))
    header("Location: $failurePage");
else
    header("Location: $successPage");
