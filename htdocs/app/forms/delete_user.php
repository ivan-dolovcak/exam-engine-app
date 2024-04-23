<?php
require_once "config.php";
session_start();

$user = UserModel::ctorLoad($_SESSION["userID"]);
if (! $user)
    die;

$user->delete();

header("Location: /app/forms/log_out.php");
