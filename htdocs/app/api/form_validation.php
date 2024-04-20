<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET" && ! isset($_GET["request"]))
    die;

$DB = DB::getInstance();

echo match($_GET["request"]) {
    "isUserEmailTaken" => $DB->isTaken(
        "User", "email", $_GET["value"]) ? LANG["emailTakenError"] : null,
    "isUsernameTaken" => $DB->isTaken(
        "User", "username", $_GET["value"]) ? LANG["usernameTakenError"] : null,
};
