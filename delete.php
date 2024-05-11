<?php 

require_once "DBconnect.php";

$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();

$id = $_POST['id'];

$statement = $pdo -> prepare('DELETE FROM emails WHERE id = :id');
$statement -> bindValue(':id', $id);
$statement -> execute();

header("Location: admin-page.php");