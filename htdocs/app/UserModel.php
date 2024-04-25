<?php
class UserModel
{
    public readonly int $ID;
    public string $username;
    public string $email;
    public string $passwordHash;
    public string $firstName;
    public string $lastName;
    public readonly string $creationDate;
    public string $lastLoginTime;
    # Only ASCII alphanumeric and _, between 4 and 30 chars:
    const REGEX_VALID_USERNAME = "/^\w{4,30}$/";
    # No digits, between 3 and 40 chars:
    const REGEX_VALID_NAME = "/^\D{3,40}$/";
    # Between 8 and 50 chars, at least 1 uppercase letter, at least 1 number:
    const REGEX_VALID_PASSWORD = "/^(?=.*\d)(?=.*[A-Z]).{8,50}$/";
    const UPDATE_VARS = ["username", "email", "firstName", "lastName", ];


    private function __construct() {}

    static function ctorLoad(int $ID) : self|false
    {
        $columns = ["username", "email", "passwordHash", "firstName",
            "lastName", "creationDate", "lastLoginTime", ];

        $query = sprintf("SELECT %s
            FROM `User`
            WHERE ID = ?", implode(", ", $columns));

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
        string $firstName, string $lastName) : ?string
    {
        $query = "INSERT INTO `User`(`username`, `email`, `passwordHash`,
                `firstName`, `lastName`)
            VALUES(?, ?, ?, ?, ?)";

        $DB = DB::getInstance();

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $errorMsg = $DB->execStmt($query, "sssss", $username, $email,
            $passwordHash, $firstName, $lastName);

        if (gettype($errorMsg) === "string")
            return $errorMsg;

        $_SESSION["userID"] = $DB->conn->insert_id; # Logged in.

        return null;
    }

    static function logIn(string $usernameOrEmail, string $password)
        : string|false|null
    {
        $query = "SELECT `ID`, `passwordHash`
            FROM `User`
            WHERE `username` = ? OR `email` = ?";

        $DB = DB::getInstance();

        $result = $DB->execStmt($query, "ss", $usernameOrEmail, $usernameOrEmail);
        if (gettype($result) === "string")
            return $result; # Error message.

        $resultRow = $result->fetch_assoc();
        $ID = $resultRow["ID"];
        $passwordHash = $resultRow["passwordHash"];

        if (! $ID || ! password_verify($password, $passwordHash))
            return false;

        $_SESSION["userID"] = $ID; # Logged in.

        # Touch last login timestamp.
        $query = "UPDATE `User`
            SET `lastLoginTime` = utc_timestamp()
            WHERE `ID` = ?";

        $DB->execStmt($query, "i", $_SESSION["userID"]);

        return null;
    }

    function update() : ?string
    {
        $DB = DB::getInstance();
        $updatePairs = ["username", "email", "firstName", "lastName", ];
        foreach ($updatePairs as &$updateVar)
            $updateVar .= sprintf(' = "%s"',
                $DB->conn->real_escape_string($this->$updateVar));

        $query = sprintf("UPDATE `User`
            SET %s
            WHERE `ID` = ?", implode(", ", $updatePairs));

        $DB = DB::getInstance();

        $errorMsg = $DB->execStmt($query, "i", $this->ID);
        if (gettype($errorMsg) === "string")
            return $errorMsg;

        return null;
    }

    function delete() : void
    {
        $query = "DELETE from `User`
            WHERE `ID` = ?";

        $DB = DB::getInstance();
        $DB->execStmt($query, "i", $this->ID);
    }
}
