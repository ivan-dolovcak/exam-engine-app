<?php
require "sql_auth.php";

class DB
{
    private static self $obj;
    public MySQLi $conn;


    private function __construct()
    {
        $this->conn = new MySQLi(
            SQL_HOSTNAME, SQL_USERNAME, SQL_PASSWORD, SQL_DATABASE);
    }

    static function getInstance(): self
    {
        if (! isset(self::$obj))
            self::$obj = new self;

        return self::$obj;
    }

    function execStmt(
        string $query, string $types, mixed ...$queryArgs): MySQLi_result|false
    {
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$queryArgs);
        $stmt->execute();
        return $stmt->get_result();
    }

    function __destruct() {
        $this->conn->close();
    }
}
