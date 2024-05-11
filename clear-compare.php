<?php 

require_once "DBconnect.php";

$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();
$statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 0');

//$statement -> bindValue(':prodID', $prodID);
$statement -> execute();

header("Location: grocery-list.php");