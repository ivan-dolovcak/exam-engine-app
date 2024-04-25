<?php
require_once "config.php";
session_start();

if (isset($_GET["documentID"])) {
    $document = DocumentModel::ctorLoad($_GET["documentID"]);

    if ($document->isAuthorized())
        $document->delete();

    header("Location: /views/index.phtml");
}
