<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/Projects/InProgress/Boutique/core/init.php';
    unset($_SESSION['User']);
    header('Location: login.php');

?>