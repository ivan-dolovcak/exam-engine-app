<?php
class Preferences
{
    const COOKIE_NAME = "examEnginePreferences";
    const DEFAULT_PREFERENCES = [
        "theme" => "light",
        "lang" => "en",
        "accentColor" => "#00AA00",
    ];
    const VALID_PREFERENCES = [
        "theme" => ["dark", "light", ],
        "lang" => ["en", "hr", ],
        "reset" => null,
        "accentColor" => null,
    ];


    private function __construct() {}

    static function savePreferences($preferences): void
    {
        setcookie(self::COOKIE_NAME, json_encode($preferences),
            strtotime("+1 year"), "/");
    }

    static function loadPreferences(): array
    {
        if (! isset($_COOKIE[self::COOKIE_NAME]))
            $preferences = self::DEFAULT_PREFERENCES;
        else
            $preferences = json_decode($_COOKIE[self::COOKIE_NAME],
                associative:true);

        return $preferences;
    }
}
