<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET")
    die;

if ($_GET["name"] === "theme")
    $preferences["theme"] = $preferences["theme"] === "light" ? "dark" : "light";
elseif ($_GET["name"] === "lang")
    $preferences["lang"] = $preferences["lang"] === "en" ? "hr" : "en";

setcookie(PREFERENCES_COOKIE_NAME, json_encode($preferences),
    strtotime("+1 year"), "/");

header("Location: " . $_SERVER["HTTP_REFERER"]); # Refresh
