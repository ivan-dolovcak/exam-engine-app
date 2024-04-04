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

# Only ASCII alphanumeric and _, at least 4 chars long:
const REGEX_VALID_USERNAME = "/^\w{4,30}$/";
# No digits:
const REGEX_VALID_NAME = "/^\D{3,40}$/";
# At least 8 chars long, at least 1 uppercase letter, at least 1 number:
const REGEX_VALID_PASSWORD = "/^(?=.*\d)(?=.*[A-Z]).{8,50}$/";

if (! preg_match(REGEX_VALID_USERNAME, $username)) {
    $_SESSION["formErrorMsg"] = LANG["invalidUsername"];
}
elseif (! preg_match(REGEX_VALID_NAME, $firstName)
    || ! preg_match(REGEX_VALID_NAME, $lastName))
{
    $_SESSION["formErrorMsg"] = LANG["invalidName"];
}
elseif (! preg_match(REGEX_VALID_PASSWORD, $password)) {
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
