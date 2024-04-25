<?php
require_once "config.php";
session_start();

if (isset($_GET["updateID"])) {
    $successPage = $_SERVER["HTTP_REFERER"];
    $failurePage = $_SERVER["HTTP_REFERER"];
}
else {
    $successPage = "/views/index.phtml";
    $failurePage = "/views/index.phtml";
}

$requiredPostVars = ["name", "type", "visibility", "timezone", ];

# Test if all required POST vars are present:
if ($_SERVER["REQUEST_METHOD"] !== "POST"
    || array_diff($requiredPostVars, array_keys($_POST)))
{
    $_SESSION["formErrorMsg"] = LANG["invalidPost"];
    header("Location: $failurePage");
    die;
}

# Create sanitized vars from POST:
foreach (array_keys($_POST) as $postVar)
    $$postVar = Util::sanitizeFormData($_POST[$postVar]);

# Adjust deadline date and time to UTC.
if ($deadlineDatetime) {
    $deadlineObj = new DateTime($deadlineDatetime, new DateTimeZone($timezone));
    $deadlineObj->setTimezone(new DateTimeZone("UTC"));
    $deadlineDatetime = $deadlineObj->format("Y-m-d H:i:s");
}

if ($numMaxSubmissions)
    $numMaxSubmissions = (int) $numMaxSubmissions;

# Validation
if (! preg_match(DocumentModel::REGEX_VALID_NAME, $name)) {
    $_SESSION["formErrorMsg"] = LANG["invalidName"];
}
elseif (isset($numMaxSubmissions) && $numMaxSubmissions >= 1
    && $numMaxSubmissions >= 100)
{
    $_SESSION["formErrorMsg"] = LANG["invalidNumMaxSubmissions"];
}

if (isset($_SESSION["formErrorMsg"])) {
    header("Location: $failurePage");
    die;
}

if (isset($_GET["updateID"])) {
    $updateVars = ["name", "type", "visibility", "numMaxSubmissions",
        "deadlineDatetime", ];
    $document = DocumentModel::ctorLoad($_GET["updateID"]);

    foreach ($updateVars as $updateVar) {
        $document->$updateVar = $$updateVar;
    }

    $errorMsg = $document->update();
}
else {
    $errorMsg = DocumentModel::create($name, $type, $visibility,
        $numMaxSubmissions ?? null, $deadlineDatetime ?? null);

    if (isset($errorMsg))
        $_SESSION["formErrorMsg"] = LANG["dbError"] . ": $errorMsg";
}

if (isset($_SESSION["formErrorMsg"]))
    header("Location: $failurePage");
else
    header("Location: $successPage");
