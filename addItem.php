<?php 

require_once "DBconnect.php";

$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();

$stores = '';
$yourErrorMessage = 'Choose another store';

//$statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 0');
//$statement -> execute();

$store1 = $_POST['store1'];

$statement = $pdo -> prepare('SELECT Store_Name FROM STORES WHERE selectPosition not in (0, 1)');
$statement -> execute();
$stores = $statement -> fetchAll(PDO::FETCH_ASSOC);

foreach($stores as $store):
    if ($store['Store_Name'] === $store1) { $store1 = ''; trigger_error($yourErrorMessage, E_USER_NOTICE);}
endforeach;

if ($store1) {
    $statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 0 WHERE selectPosition = 1');
    $statement -> execute();

    $statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 1 WHERE Store_Name = :selectedStore');
    $statement -> bindValue(':selectedStore', $store1);
    $statement -> execute();
}

$store2 = $_POST['store2'];

$statement = $pdo -> prepare('SELECT Store_Name FROM STORES WHERE selectPosition not in (0, 2)');
$statement -> execute();
$stores = $statement -> fetchAll(PDO::FETCH_ASSOC);

foreach($stores as $store):
    if ($store['Store_Name'] === $store2) { $store2 = ''; }
endforeach;

if ($store2) {
    $statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 0 WHERE selectPosition = 2');
    $statement -> execute();

    $statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 2 WHERE Store_Name = :selectedStore');
    $statement -> bindValue(':selectedStore', $store2);
    $statement -> execute();
}

$store3 = $_POST['store3'];

$statement = $pdo -> prepare('SELECT Store_Name FROM STORES WHERE selectPosition not in (0, 3)');
$statement -> execute();
$stores = $statement -> fetchAll(PDO::FETCH_ASSOC);

foreach($stores as $store):
    if ($store['Store_Name'] === $store3) { $store3 = ''; }
endforeach;

if ($store3) {
    $statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 0 WHERE selectPosition = 3');
    $statement -> execute();

    $statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 3 WHERE Store_Name = :selectedStore');
    $statement -> bindValue(':selectedStore', $store3);
    $statement -> execute();
}


/*
$store2 = $_POST['store2'];

$statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 2 WHERE Store_Name = :selectedStore');
$statement -> bindValue(':selectedStore', $store2);
$statement -> execute();

$store3 = $_POST['store3'];

$statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 3 WHERE Store_Name = :selectedStore');
$statement -> bindValue(':selectedStore', $store3);
$statement -> execute();
*/
header("Location: quick-compare.php");