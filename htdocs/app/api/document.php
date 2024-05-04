<?php
require_once "config.php";
session_start();

if (!isset($_GET["documentID"], $_GET["request"]))
    die;

if ($_GET["request"] === "load") {
    $document = DocumentModel::ctorLoad($_GET["documentID"], doLoadContent:true);
    $document->documentJSON ??= LANG["emptyDocument"];
    echo json_encode($document);
}
elseif ($_GET["request"] === "submission") {
    $submissionJSON = file_get_contents("php://input");
}
