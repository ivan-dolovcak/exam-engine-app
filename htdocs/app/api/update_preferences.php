<?php
require_once "config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "GET" || ! isset($_GET["name"]))
    die;

$availablePreferences = ["theme", "lang", "reset", ];
$availableThemes = ["dark", "light", ];
$availableLangs = ["hr", "en", ];

if ($_GET["name"] === "theme" && ! in_array($_GET["value"], $availableThemes)) {
    $_SESSION["formErrorMsg"] = LANG["invalidPreferences"];
}
elseif ($_GET["name"] === "lang" && ! in_array($_GET["value"], $availableLangs)) {
    $_SESSION["formErrorMsg"] = LANG["invalidPreferences"];
}
elseif (! in_array($_GET["name"], $availablePreferences)) {
    $_SESSION["formErrorMsg"] = LANG["invalidPreferences"];
}

if (! isset($_SESSION["formErrorMsg"])) {
    if ($_GET["name"] === "reset")
        $preferences = DEFAULT_PREFERENCES;
    else
        $preferences[$_GET["name"]] = $_GET["value"];

    setcookie(PREFERENCES_COOKIE_NAME, json_encode($preferences),
        strtotime("+1 year"), "/");
}

# Return to view (previous page):
header("Location: " . $_SERVER["HTTP_REFERER"]);
