<?php
class DocumentModel
{
    public readonly int $ID;
    public string $name;
    public string $type;
    public string $visibility;
    public int $numMaxSubmissions;
    public ?string $deadlineDatetime;
    public ?string $documentJSON;
    public ?string $solutionJSON;
    public readonly int $authorID;
    public readonly string $creationDate;
    const REGEX_VALID_NAME = "/^.{4,50}$/";

    private function __construct() {}

    public static function create(string $name, string $type, string $visibility,
        ?int $numMaxSubmissions, ?string $deadlineDatetime) : ?string
    {
        $query = "INSERT INTO `Document`(`name`, `type`, `visibility`,
            `numMaxSubmissions`, `deadlineDatetime`, `authorID`)
            VALUES(?, ?, ?, ?, ?, ?)";

        $DB = DB::getInstance();

        $errorMsg = $DB->execStmt($query, "sssisi", $name, $type, $visibility,
            $numMaxSubmissions, $deadlineDatetime, $_SESSION["userID"]);

        if (gettype($errorMsg) === "string")
            return $errorMsg;

        return null;
    }

    public static function listDocuments(?string $filter) : array {
        $query = "SELECT `Document`.`ID`, `name`, `type`, `visibility`,
            `numMaxSubmissions`, `authorID`, `firstName`, `lastName`, `username`,
            `deadlineDatetime`, `Document`.`creationDate`
            FROM `Document`
            INNER JOIN `User`
                ON `User`.`ID` = `authorID`";

        if (isset($filter))
            $query .= "WHERE " . $filter;

        $DB = DB::getInstance();

        $result = $DB->execStmt($query, null);

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
