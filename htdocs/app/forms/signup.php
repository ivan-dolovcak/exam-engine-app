<?php
require_once "config.php";
session_start();

$successPage = "/views/index.phtml";
$failurePage = "/views/signup.phtml";

if ($_SERVER["REQUEST_METHOD"] !== "POST"
    || ! isset($_POST["username"], $_POST["email"], $_POST["password"],
        $_POST["firstName"],$_POST["lastName"]))
{
    $_SESSION["formErrMsg"] = LANG["invalidPost"];
    header("Location: $failurePage");
    die;
}

$username   = Util::sanitizeFormData($_POST["username"]);
$email      = Util::sanitizeFormData($_POST["email"]);
$password   = Util::sanitizeFormData($_POST["password"]);
$firstName  = Util::sanitizeFormData($_POST["firstName"]);
$lastName   = Util::sanitizeFormData($_POST["lastName"]);

$user = UserModel::ctorSignUp(
    $username, $email, $password, $firstName, $lastName);

$errorMsg = $user->signUp();
if (isset($errorMsg)) {
    if (str_contains($errorMsg, "UK_username"))
        $_SESSION["formErrMsg"] = LANG["usernameTakenError"];
    elseif (str_contains($errorMsg, "UK_email"))
        $_SESSION["formErrMsg"] = LANG["emailTakenError"];
    else
        $_SESSION["formErrMsg"] = LANG["dbError"];

    header("Location: $failurePage");
}
else
    header("Location: $successPage");
