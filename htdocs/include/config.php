<?php
declare(strict_types=1);

if (isset($_SERVER["DEVELOPMENT"])) {
    error_reporting(E_ALL);
    ini_set("display_errors", true);
    ini_set("display_startup_errors", true);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

date_default_timezone_set("UTC");

# Auto-require classes:
spl_autoload_register(fn($className) => require "$className.php");
