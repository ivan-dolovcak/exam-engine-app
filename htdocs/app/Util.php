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
    public static function sanitizeFormData(string $data): ?string
    {
        $sanitizedData = htmlspecialchars(stripslashes(trim($data)));
        if (empty($sanitizedData))
            return null;
        return $sanitizedData;
    }

    public static function colorHex2RGB(string $hexColor) : string {
        $hexColor = trim($hexColor, "#");
        list($r, $g, $b) = array_map(
            fn($x) => hexdec($x), str_split($hexColor, 2));
        return "$r, $g, $b";
    }

    public static function redirect(string $URL) : void
    {
        header("Location: $URL");
        die;
    }
}
