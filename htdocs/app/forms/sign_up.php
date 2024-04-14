<?php
require_once "config.php";
session_start();

$successPage = "/views/index.phtml";
$failurePage = "/views/sign_up.phtml";

$requiredPostVars = ["username", "email", "password", "firstName", "lastName", ];

if ($_SERVER["REQUEST_METHOD"] !== "POST"
    || array_diff($requiredPostVars, array_keys($_POST)))
{
    $_SESSION["formErrorMsg"] = LANG["invalidPost"];
    header("Location: $failurePage");
    die;
}

foreach ($requiredPostVars as $postVar)
    $$postVar = Util::sanitizeFormData($_POST[$postVar]);

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
    $DB = DB::getInstance();
    if ($DB->isTaken("User", "username", $username))
        $_SESSION["formErrorMsg"] = LANG["usernameTakenError"];
    elseif ($DB->isTaken("User", "email", $email))
        $_SESSION["formErrorMsg"] = LANG["emailTakenError"];
    else {
        $errorMsg = UserModel::signUp($username, $email, $password, $firstName,
            $lastName);
        if (isset($errorMsg))
            $_SESSION["formErrorMsg"] = LANG["dbError"] . ": $errorMsg";
    }
}

if (isset($_SESSION["formErrorMsg"]))
    header("Location: $failurePage");
else
    header("Location: $successPage");
