<?php
require_once "config.php";
session_start();

if (!isset($_GET["documentID"], $_GET["request"]))
    die;

if (isset($_GET["submissionID"])) {
    $submission = SubmissionModel::ctorLoad($_GET["submissionID"], doLoadContent:true);
    echo json_encode($submission);
}
elseif ($_GET["request"] === "load") {
    $document = DocumentModel::ctorLoad($_GET["documentID"], doLoadContent:true);
    $document->documentJSON ??= LANG["emptyDocument"];
    echo json_encode($document);
}
elseif ($_GET["request"] === "submission") {
    $submissionJSON = file_get_contents("php://input");

    $_SESSION["formErrorMsg"] = SubmissionModel::create($_GET["documentID"],
        $submissionJSON);
}
elseif ($_GET["request"] === "editDocumentQuestions") {
    $data = file_get_contents("php://input");
    list($documentJSON, $solutionJSON) = json_decode($data);

    $document = DocumentModel::ctorLoad($_GET["documentID"]);
    $document->documentJSON = json_encode($documentJSON);
    $document->solutionJSON = json_encode($solutionJSON);

    $_SESSION["formErrorMsg"] = $document->update();
}
