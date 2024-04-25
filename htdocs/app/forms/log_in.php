<?php
require_once "config.php";
session_start();

$successPage = "/views/index.phtml";
$failurePage = "/views/log_in.phtml";

if ($_SERVER["REQUEST_METHOD"] !== "POST"
    || ! isset($_POST["usernameOrEmail"], $_POST["password"]))
{
    $_SESSION["formErrorMsg"] = LANG["invalidPost"];
    Util::redirect("$failurePage");
}

$usernameOrEmail = Util::sanitizeFormData($_POST["usernameOrEmail"]);
$password        = Util::sanitizeFormData($_POST["password"]);

$errorMsg = UserModel::logIn($usernameOrEmail, $password);

if (isset($errorMsg)) {
    if ($errorMsg === false)
        $_SESSION["formErrorMsg"] = LANG["invalidLogin"];
    else
        $_SESSION["formErrorMsg"] = LANG["dbError"] . ": $errorMsg";

    Util::redirect("$failurePage");
}
else
    Util::redirect("$successPage");
