<?php
class UserModel
{
    public readonly int $ID;
    public readonly string $username;
    public string $email;
    public string $passwordHash;
    public string $firstName;
    public string $lastName;
    public readonly string $creationDate;
    public string $lastLoginTime;
    # Only ASCII alphanumeric and _, at least 4 chars long:
    const REGEX_VALID_USERNAME = "/^\w{4,30}$/";
    # No digits:
    const REGEX_VALID_NAME = "/^\D{3,40}$/";
    # At least 8 chars long, at least 1 uppercase letter, at least 1 number:
    const REGEX_VALID_PASSWORD = "/^(?=.*\d)(?=.*[A-Z]).{8,50}$/";


    # Overloaded ctors:
    private function __construct() {}

    static function ctorLoad(int $ID): self|false
    {
        $columns = ["username", "email", "passwordHash", "firstName",
            "lastName", "creationDate", "lastLoginTime", ];
        $query = "select " . implode(", ", $columns) .  " from `User`
            where ID = ?";

        $DB = DB::getInstance();
        $result = $DB->execStmt($query, "i", $ID);

        if ($result->num_rows === 0)
            return false;

        $resultRow = $result->fetch_assoc();

        $instance = new self;
        $instance->ID = $ID;
        foreach ($columns as $column) {
            $instance->$column = $resultRow[$column];
        }

        return $instance;
    }

    static function signUp(string $username, string $email, string $password,
        string $firstName, string $lastName): ?string
    {
        $query = "insert into `User`(`username`, `email`, `passwordHash`,
            `firstName`, `lastName`)
            values(?, ?, ?, ?, ?)";

        $DB = DB::getInstance();

        try {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $DB->execStmt($query, "sssss", $username, $email, $passwordHash,
                $firstName, $lastName);
        }
        catch (MySQLi_SQL_exception $e) {
            return $e->getMessage();
        }

        $_SESSION["userID"] = $DB->conn->insert_id; # Logged in

        return null;
    }

    static function logIn(string $usernameOrEmail, string $password)
        : string|false|null
    {
        $query = "select `ID`, `passwordHash` from `User`
            where `username` = ? or `email` = ?";

        $DB = DB::getInstance();

        try {
            $result = $DB->execStmt($query, "ss", $usernameOrEmail,
                $usernameOrEmail);
        }
        catch (MySQLi_SQL_exception $e) {
            return $e->getMessage();
        }

        $resultRow = $result->fetch_assoc();
        $ID = $resultRow["ID"];
        $passwordHash = $resultRow["passwordHash"];

        if (! $ID || ! password_verify($password, $passwordHash))
            return false;

        $_SESSION["userID"] = $ID; # Logged in

        # Touch last login timestamp.
        $query = "update `User` set `lastLoginTime` = utc_timestamp()
            where ID = ?";

        try {
            $result = $DB->execStmt(
                $query, "i", $_SESSION["userID"]);
        }
        catch (MySQLi_SQL_exception $e) {
            return $e->getMessage();
        }

        return null;
    }
}
