<?php
declare(strict_types=1);

# Display errors only on local development server:
if (isset($_SERVER["DEVELOPMENT"])) {
    error_reporting(E_ALL);
    ini_set("display_errors", true);
    ini_set("display_startup_errors", true);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

date_default_timezone_set("UTC");

# Auto-require classes:
spl_autoload_register(fn($className) => require "$className.php");

const PREFERENCES_COOKIE_NAME = "examEnginePreferences";
const DEFAULT_PREFERENCES = ["theme" => "light", "lang" => "en"];
# Load/set default user preferences:
if (! isset($_COOKIE[PREFERENCES_COOKIE_NAME]))
    $preferences = DEFAULT_PREFERENCES;
else
    $preferences = json_decode($_COOKIE[PREFERENCES_COOKIE_NAME],
        associative:true);

# Multi-language support:
require_once "lang_{$preferences["lang"]}.php";
