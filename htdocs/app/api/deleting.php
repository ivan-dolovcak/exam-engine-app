<?php
require_once "config.php";
session_start();

if (isset($_GET["documentID"])) {
    $document = DocumentModel::ctorLoad($_GET["documentID"]);

    if ($document && $document->isAuthorized()) {
        $document->delete();
        Util::redirect("/views/index.phtml");
    }
}
elseif (isset($_GET["user"])) {
    $user = UserModel::ctorLoad($_SESSION["userID"]);

    if ($user) {
        $user->delete();
        Util::redirect("/app/forms/log_out.php");
    }
}
else
    Util::redirect($_SERVER["HTTP_REFERER"]);
