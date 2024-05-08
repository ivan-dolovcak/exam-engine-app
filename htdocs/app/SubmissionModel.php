<?php
class SubmissionModel
{
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
