<?php
class SubmissionModel
{
    public readonly int $ID;
    public readonly int $documentID;
    public readonly int $userID;
    public readonly string $creationDate;
    public string $submissionJSON;
    public ?string $gradesJSON;

    public static function create(int $documentID, string $submissionJSON)
        : ?string
    {
        $query = "INSERT INTO `Submission`(`documentID`, `userID`,
            `submissionJSON`)
            VALUES (?, ?, ?)";

        $DB = DB::getInstance();

        $errorMsg = $DB->execStmt($query, "iis", $documentID,
            $_SESSION["userID"], $submissionJSON);

        if (gettype($errorMsg) === "string")
            return $errorMsg;

        return null;
    }

    public static function ctorLoad(int $ID, bool $doLoadContent = false)
        : self|false
    {
        $columns = ["documentID" , "userID", "creationDate", ];

        if ($doLoadContent)
            array_push($columns, "submissionJSON");

        $query = sprintf("SELECT %s
            FROM `Submission`
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

    public static function listSubmissions(?string $filter) : array
    {
        $query = "SELECT `Submission`.`ID` as `submissionID`,
            `userID`, `firstName`, `lastName`, `username`,
            `documentID`, `Submission`.`creationDate` as `submissionDate`
            FROM `Submission`
            INNER JOIN `User`
                ON `User`.`ID` = `userID`";

        if (isset($filter))
            $query .= "WHERE " . $filter;

        $DB = DB::getInstance();

        $result = $DB->execStmt($query, null);

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
