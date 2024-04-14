<?php
class Util
{
    public static function getFormError(): ?string
    {
        $errMsg = $_SESSION["formErrorMsg"] ?? null;
        unset($_SESSION["formErrorMsg"]);
        return $errMsg;
    }

    # For preventing XSS-attacks.
    public static function sanitizeFormData(string $data): string
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}
