<?php
require_once "config.php";
session_start();
session_destroy();
Util::redirect("/views/index.phtml");
