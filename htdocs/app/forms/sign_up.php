<?php
require_once "config.php";
session_start();

if (isset($_GET["update"])) {
    $successPage = "/views/profile.phtml";
    $failurePage = "/views/profile.phtml";
} else {
    $successPage = "/views/index.phtml";
    $failurePage = "/views/sign_up.phtml";
}

$requiredPostVars = ["username", "email", "firstName", "lastName", ];
if (! isset($_GET["update"]))
    $requiredPostVars[] = "password";

# Test if all required POST vars are present:
if ($_SERVER["REQUEST_METHOD"] !== "POST"
    || array_diff($requiredPostVars, array_keys($_POST)))
{
    $_SESSION["formErrorMsg"] = LANG["invalidPost"];
    header("Location: $failurePage");
    die;
}

# Create sanitized vars from POST:
foreach ($requiredPostVars as $postVar)
    $$postVar = Util::sanitizeFormData($_POST[$postVar]);

# Validation
if (! preg_match(UserModel::REGEX_VALID_USERNAME, $username)) {
    $_SESSION["formErrorMsg"] = LANG["invalidUsername"];
}
elseif (! preg_match(UserModel::REGEX_VALID_NAME, $firstName)
    || ! preg_match(UserModel::REGEX_VALID_NAME, $lastName))
{
    $_SESSION["formErrorMsg"] = LANG["invalidName"];
}
elseif (! isset($_GET["update"])
    && ! preg_match(UserModel::REGEX_VALID_PASSWORD, $password))
{
    $_SESSION["formErrorMsg"] = LANG["invalidPassword"];
}
elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)
    || ! checkdnsrr(substr($email, strpos($email, "@")+1)))
{
    $_SESSION["formErrorMsg"] = LANG["invalidEmail"];
}
else if (! isset($_GET["update"])) {
    $DB = DB::getInstance();
    if ($DB->isTaken("User", "username", $username))
        $_SESSION["formErrorMsg"] = LANG["usernameTakenError"];
    elseif ($DB->isTaken("User", "email", $email))
        $_SESSION["formErrorMsg"] = LANG["emailTakenError"];
}

if (isset($_SESSION["formErrorMsg"])) {
    header("Location: $failurePage");
    die;
}

if (isset($_GET["update"])) {
    $updateVars = ["username", "email", "firstName", "lastName", ];
    $user = UserModel::ctorLoad($_SESSION["userID"]);

    foreach ($updateVars as $updateVar) {
        $user->$updateVar = $$updateVar;
    }

    $errorMsg = $user->update();

} else {
    $errorMsg = UserModel::signUp($username, $email, $password, $firstName,
        $lastName);
}

if (isset($errorMsg))
    $_SESSION["formErrorMsg"] = LANG["dbError"] . ": $errorMsg";

if (isset($_SESSION["formErrorMsg"]))
    header("Location: $failurePage");
else
    header("Location: $successPage");
