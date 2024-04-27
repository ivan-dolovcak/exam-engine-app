<?php
require "sql_auth.php";

# Singleton DB controller with boilerplate code.
class DB
{
    private static self $obj;
    public MySQLi $conn;


    private function __construct()
    {
        $this->conn = new MySQLi(
            SQL_HOSTNAME, SQL_USERNAME, SQL_PASSWORD, SQL_DATABASE);
    }

    static function getInstance() : self
    {
        if (! isset(self::$obj))
            self::$obj = new self;

        return self::$obj;
    }

    function execStmt(string $query, ?string $types, mixed ...$queryArgs)
        : MySQLi_result|false|string
    {
        try {
            $stmt = $this->conn->prepare($query);
            if (isset($types))
                $stmt->bind_param($types, ...$queryArgs);
            $stmt->execute();
            return $stmt->get_result();
        }
        catch (MySQLi_SQL_exception $e) {
            return $e->getMessage();
        }
    }

    # For testing if column value is unique.
    function isTaken(string $table, string $column, string $value) : bool|string
    {
        $query = "SELECT `$column`
            FROM `$table`
            WHERE `$column` = ?";

        $DB = self::getInstance();
        $result = $DB->execStmt($query, "s", $value);

        if (gettype($result) === "string")
            return $result;
        elseif ($result->num_rows > 0)
            return true;
        else
            return false;
    }

    function __destruct()
    {
        $this->conn->close();
    }
}
