<?php 

require_once "DBconnect.php";

$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();

$stores = '';
$yourErrorMessage = 'Choose another store';

function updateStore($storeNumber, $position, $pdo) {

    $statement = $pdo -> prepare('SELECT Store_Name FROM STORES WHERE selectPosition not in (0, :position)');
    $statement -> bindValue(':position', $position);
    $statement -> execute();
    $stores = $statement -> fetchAll(PDO::FETCH_ASSOC);

    foreach($stores as $store):
        if ($store['Store_Name'] === $storeNumber) { $storeNumber = '';}
    endforeach;

    if ($storeNumber) {
        $statement = $pdo -> prepare('UPDATE STORES SET selectPosition = 0 WHERE selectPosition = :position');
        $statement -> bindValue(':position', $position);
        $statement -> execute();

        $statement = $pdo -> prepare('UPDATE STORES SET selectPosition = :position WHERE Store_Name = :selectedStore');
        $statement -> bindValue(':position', $position);
        $statement -> bindValue(':selectedStore', $storeNumber);
        $statement -> execute();
    }
}

updateStore($_POST['store1'], 1,$pdo);
updateStore($_POST['store2'], 2,$pdo);
updateStore($_POST['store3'], 3,$pdo);

header("Location: quick-compare.php");
