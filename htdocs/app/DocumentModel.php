<?php
class DocumentModel
{
    public readonly int $ID;
    public string $name;
    public string $type;
    public string $visibility;
    public ?int $numMaxSubmissions;
    public ?string $deadlineDatetime;
    public ?string $documentJSON;
    public ?string $solutionJSON;
    public readonly int $authorID;
    public readonly string $creationDate;

    const REGEX_VALID_NAME = "/^.{4,50}$/";
    const UPDATE_VARS = ["name", "type", "visibility", "deadlineDatetime",
        "numMaxSubmissions", ];


    private function __construct() {}

    public static function ctorLoad(int $ID, bool $doLoadContent = false)
        : self|false
    {
        $columns = ["name", "type", "visibility", "numMaxSubmissions",
            "deadlineDatetime", "authorID", "creationDate", ];

        if ($doLoadContent)
            array_push($columns, "documentJSON", "solutionJSON");

        $query = sprintf("SELECT %s
            FROM `Document`
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

    public static function listDocuments(?string $filter) : array
    {
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

    public function isAuthorized() : bool
    {
        return $this->authorID === $_SESSION["userID"] ?? null;
    }

    function delete() : void
    {
        $query = "DELETE from `Document`
            WHERE `ID` = ?";

        $DB = DB::getInstance();
        $DB->execStmt($query, "i", $this->ID);
    }

    function update() : ?string
    {
        $DB = DB::getInstance();
        $updatePairs = ["name", "type", "visibility", "deadlineDatetime",
            "numMaxSubmissions", ];
        $types = "ssssii";

        foreach ($updatePairs as &$updateVar)
            $updateVar .= " = ?";

        $query = sprintf("UPDATE `Document`
            SET %s
            WHERE `ID` = ?", implode(", ", $updatePairs));

        $DB = DB::getInstance();

        $errorMsg = $DB->execStmt($query, $types, $this->name, $this->type,
            $this->visibility, $this->deadlineDatetime, $this->numMaxSubmissions,
            $this->ID);

        if (gettype($errorMsg) === "string")
            return $errorMsg;

        return null;
    }
}
