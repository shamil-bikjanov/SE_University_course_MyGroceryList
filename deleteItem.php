<?php 

require_once "DBconnect.php";

$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();

$statement = $pdo -> prepare('TRUNCATE TABLE MyGroceryList');
$statement -> execute();

$statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 0');
$statement -> execute();

header("Location: grocery-list.php");