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
}
