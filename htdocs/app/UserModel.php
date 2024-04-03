<?php
class UserModel
{
    public readonly int $ID;
    public string $username;
    public string $email;
    public ?string $password;
    public string $passwordHash;
    public string $firstName;
    public string $lastName;
    public readonly string $creationDate;
    public ?string $lastLoginTime;


    private function __construct() {}


    # Used when creating a new user (signing up).
    static function ctorSignUp(string $username, string $email,
        string $password, string $firstName, string $lastName): self
    {
        $instance = new self;

        $instance->username = $username;
        $instance->email = $email;
        $instance->firstName = $firstName;
        $instance->lastName = $lastName;
        $instance->passwordHash = password_hash($password, PASSWORD_BCRYPT);

        return $instance;
    }

    static function ctorLogIn(string $usernameOrEmail, string $password)
    {
        $instance = new self;

        $instance->username = $usernameOrEmail;
        $instance->password = $password;

        return $instance;
    }

    function signUp(): ?string
    {
        $query = "insert into `User`(`username`, `email`, `passwordHash`,
            `firstName`, `lastName`) values (?, ?, ?, ?, ?)";

        $db = DB::getInstance();

        try {
            $db->execStmt($query, "sssss", $this->username, $this->email,
                $this->passwordHash, $this->firstName, $this->lastName);
        }
        catch (Mysqli_sql_exception $e) {
            return $e->getMessage();
        }

        $this->ID = $db->conn->insert_id;
        $_SESSION["userID"] = $db->conn->insert_id;
        $this->creationDate = date("Y.m.d");
        $this->lastLoginTime = null;

        return null;
    }

    function logIn(): string|false|null
    {
        $query = "select `ID`, `passwordHash` from `User`
            where `username` = ? or `email` = ?";

        $db = DB::getInstance();

        try {
            $result = $db->execStmt(
                $query, "ss", $this->username, $this->username);
        }
        catch (Mysqli_sql_exception $e) {
            return $e->getMessage();
        }

        $resultRow = $result->fetch_assoc();
        $ID = $resultRow["ID"];
        $passwordHash = $resultRow["passwordHash"];

        if (! $ID || ! password_verify($this->password, $passwordHash))
            return false;

        $_SESSION["userID"] = $ID;

        return null;
    }
}
