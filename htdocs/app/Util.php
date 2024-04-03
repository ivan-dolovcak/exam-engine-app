<?php
require_once "config.php";

class Util
{
    public static function getFormError(): ?string
    {
        $errMsg = $_SESSION["formErrMsg"] ?? null;
        unset($_SESSION["formErrMsg"]);
        return $errMsg;
    }
    public static function sanitizeFormData(string $data): string
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}
