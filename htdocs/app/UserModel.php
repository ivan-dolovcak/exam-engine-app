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
        $this->creationDate = date("Y.m.d");
        $this->lastLoginTime = null;

        return null;
    }
}
