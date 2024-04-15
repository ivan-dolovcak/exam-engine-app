<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET" || ! isset($_GET["name"]))
    die;

if ($_GET["name"] === "reset")
    $preferences = DEFAULT_PREFERENCES;
else
    $preferences[$_GET["name"]] = $_GET["value"];

setcookie(PREFERENCES_COOKIE_NAME, json_encode($preferences),
    strtotime("+1 year"), "/");

# Return to view (previous page):
header("Location: " . $_SERVER["HTTP_REFERER"]);
