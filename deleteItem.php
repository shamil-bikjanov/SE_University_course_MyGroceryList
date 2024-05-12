<?php 

require_once "DBconnect.php";
session_start();
$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();

$groceryListTitle = $_SESSION["active-userID"].'MyGroceryList';

$statement = $pdo -> prepare("TRUNCATE TABLE $groceryListTitle");
$statement -> execute();

$statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 0');
$statement -> execute();

header("Location: grocery-list.php");