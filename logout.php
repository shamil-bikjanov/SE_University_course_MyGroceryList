<?php 

require_once "DBconnect.php";

session_start();
$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();

$_SESSION["active-user"] = '';

header("Location: index.php");