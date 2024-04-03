<?php
require_once "config.php";

class Util
{
    public static function getFormError(): ?string
    {
        $errMsg = $_SESSION["formErrorMsg"] ?? null;
        unset($_SESSION["formErrorMsg"]);
        return $errMsg;
    }
    public static function sanitizeFormData(string $data): string
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}
