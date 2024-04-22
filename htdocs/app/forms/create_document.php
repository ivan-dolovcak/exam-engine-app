<?php
require_once "config.php";
session_start();

$successPage = "/views/index.phtml";
$failurePage = "/views/index.phtml";

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
    if (isset($_POST[$postVar]) && $_POST[$postVar])
        $$postVar = Util::sanitizeFormData($_POST[$postVar]);
    else
        $$postVar = null;

# Adjust deadline date and time to UTC.
if (isset($_POST["deadlineDatetime"])) {
    $deadlineObj = new DateTime($deadlineDatetime, new DateTimeZone($timezone));
    $deadlineObj->setTimezone(new DateTimeZone("UTC"));
    $deadlineDatetime = $deadlineObj->format("Y-m-d H:i:s");
}

if (isset($numMaxSubmissions))
    $numMaxSubmissions = (int) $numMaxSubmissions;

# Validation
if (! preg_match(DocumentModel::REGEX_VALID_NAME, $name)) {
    $_SESSION["formErrorMsg"] = LANG["invalidName"];
}
else if (isset($numMaxSubmissions) && $numMaxSubmissions >= 1
    && $numMaxSubmissions >= 100)
{
    $_SESSION["formErrorMsg"] = LANG["invalidNumMaxSubmissions"];
}
else {
    $errorMsg = DocumentModel::create($name, $type, $visibility,
        $numMaxSubmissions ?? null, $deadlineDatetime ?? null);
}

if (isset($errorMsg))
    $_SESSION["formErrorMsg"] = LANG["dbError"] . ": $errorMsg";

if (isset($_SESSION["formErrorMsg"]))
    header("Location: $failurePage");
else
    header("Location: $successPage");
