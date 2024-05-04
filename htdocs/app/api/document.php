<?php
require_once "config.php";
session_start();

if (isset($_GET["documentID"])) {
    $document = DocumentModel::ctorLoad($_GET["documentID"], doLoadContent:true);
    $document->documentJSON ??= LANG["emptyDocument"];
    echo json_encode($document);
}
