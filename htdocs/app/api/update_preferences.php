<?php
require_once "config.php";
session_start();

if (! isset($_GET["name"]))
    die;

if ($_GET["name"] === "theme"
    && ! in_array($_GET["value"], Preferences::VALID_PREFERENCES["theme"]))
{
    $_SESSION["formErrorMsg"] = LANG["invalidPreferences"];
}
elseif ($_GET["name"] === "lang"
    && ! in_array($_GET["value"], Preferences::VALID_PREFERENCES["lang"]))
{
    $_SESSION["formErrorMsg"] = LANG["invalidPreferences"];
}
elseif (! in_array($_GET["name"], array_keys(Preferences::VALID_PREFERENCES))) {
    $_SESSION["formErrorMsg"] = LANG["invalidPreferences"];
}

if (! isset($_SESSION["formErrorMsg"])) {
    if ($_GET["name"] === "reset")
        $preferences = Preferences::DEFAULT_PREFERENCES;
    else
        $preferences[$_GET["name"]] = $_GET["value"];

    Preferences::savePreferences($preferences);
}

# Return to view (previous page):
Util::redirect("" . $_SERVER["HTTP_REFERER"]);
