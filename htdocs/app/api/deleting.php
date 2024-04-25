<?php
require_once "config.php";
session_start();

if (isset($_GET["documentID"])) {
    $document = DocumentModel::ctorLoad($_GET["documentID"]);

    if ($document->isAuthorized())
        $document->delete();

    header("Location: /views/index.phtml");
}
elseif (isset($_GET["user"])) {
    $user = UserModel::ctorLoad($_SESSION["userID"]);
    if (! $user)
        die;

    $user->delete();

    header("Location: /app/forms/log_out.php");
}
