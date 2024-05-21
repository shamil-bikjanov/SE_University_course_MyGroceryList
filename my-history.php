<?php 

require_once "DBconnect.php";
session_start();
$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();

$selectedCategory = '';
$selectedProduct = '';
$lowestPrice = '';
$lowestPriceStore = '';
$testMessage = '';
$activeAccount = '';
$previousDate = '';
$emptyString = '--';
$sumCost = 0;

if (!$_SESSION["active-user"]) {
    header('Location: index.php');
}

$myHistoryTitle = $_SESSION["active-userID"].'MyHistory';
$groceryListTitle = $_SESSION["active-userID"].'MyGroceryList';

// MyGroceryList TABLE
$statement = $pdo->prepare("CREATE TABLE IF NOT EXISTS $myHistoryTitle
                (
                    Prod_title      varchar(50)		NOT NULL,
                    Store_Name      varchar(50)		NOT NULL,
                    Prod_price      decimal(10,2)	NOT NULL,
                    Prod_amount     int		        NOT NULL DEFAULT 1,
                    Record_date     datetime        NOT NULL
                )");
$statement -> execute();

$statement = $pdo -> prepare("  SELECT PRICES.PRODUCT_ID, PRICES.STORE_ID, PRICES.PROD_price, STORES.Store_Name
                                FROM PRICES
                                INNER JOIN STORES ON STORES.STORE_ID = PRICES.STORE_ID");
$statement -> execute();
$prices = $statement -> fetchAll(PDO::FETCH_ASSOC);

// CATEGORIES TABLE AND QUERY
$statement = $pdo -> prepare("SELECT distinct Prod_category, PRODUCT_ID FROM PRODUCTS ORDER BY PRODUCT_ID");
$statement -> execute();
$categories = $statement -> fetchAll(PDO::FETCH_ASSOC);

// PRODUCTS TABLE AND QUERY
$statement = $pdo -> prepare("  SELECT 
                                      min(PRICES.PROD_price) PROD_price
                                    , prod.Prod_title Prod_title
                                    , prod.PRODUCT_ID PRODUCT_ID
                                    , 1 Prod_amount
                                FROM $groceryListTitle groc
                                INNER JOIN PRODUCTS as prod ON groc.PRODUCT_ID = prod.PRODUCT_ID
                                INNER JOIN PRICES ON PRICES.PRODUCT_ID = groc.PRODUCT_ID
                                INNER JOIN STORES ON STORES.STORE_ID = PRICES.STORE_ID
                                GROUP BY prod.Prod_title, prod.PRODUCT_ID");
$statement -> execute();
$items = $statement -> fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach($items as $item):
        $sumCost += ($item['PROD_price'] * $item['Prod_amount']);
        $statement = $pdo->prepare("INSERT INTO $myHistoryTitle (Prod_title, Prod_price, Record_date, Store_Name)
                                VALUES (:Prod_title, :PROD_price, CURRENT_TIMESTAMP, ( SELECT Store_Name 
                                                                            FROM STORES 
                                                                            INNER JOIN PRICES ON PRICES.STORE_ID = STORES.STORE_ID
                                                                            WHERE PRICES.PROD_price = :PROD_price
                                                                            AND PRICES.PRODUCT_ID = :PRODUCT_ID
                                                                            LIMIT 1))");

        $statement -> execute([
            ':Prod_title' => $item['Prod_title'],
            ':PROD_price' => $item['PROD_price'],
            ':PRODUCT_ID' => $item['PRODUCT_ID']
        ]);
    endforeach;

    if ($items) {
        $statement = $pdo->prepare("INSERT INTO $myHistoryTitle (Prod_title, Prod_price, Record_date, Store_Name)
                                    VALUES ('--', :sumCost, CURRENT_TIMESTAMP,'--')");
        $statement -> execute([':sumCost' => $sumCost]);
        $sumCost = 0;
    }

    $statement = $pdo -> prepare("TRUNCATE TABLE $groceryListTitle");
    $statement -> execute();
}

$statement = $pdo -> prepare("SELECT * FROM $myHistoryTitle");
$statement -> execute();
$historyList = $statement -> fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="quick-compare_styles.css">
    <title>Grocery List</title>
</head>

<body>
    <aside>
    <header>
            <div class="header-container">
                <div id="icons">
                    <a href="index.php" id="p-logo"></a>
                    <a href="index.php" id="p-label"></a>
                </div>
                
                <label class="burger">
                    <input type="checkbox">
                    <span class="menu"> <span class="hamburger"></span> </span>
                    <ul>
                    <li> <a href="grocery-list.php">My Grocery List</a> </li>
                    <li> <a href="quick-compare.php">Quick compare</a> </li>
                    <li> <a href="my-history.php">My History</a> </li>
                    <?php if ($_SESSION["active-user"] === 'admin@admin.com') { ?>
                        <li><a href="admin-page.php" id="contacts"><span>Contacts</span></a></li>
                    <?php } ?> 
                    <li><a href="logout.php"><span id="logout">Log-out</span></a></li>
                    
                    </ul>
                </label>

                <div id="header-links">
                    <a href="grocery-list.php"><span>My Grocery List</span></a>
                    <a href="quick-compare.php"><span>Quick compare</span></a>
                    <a href="my-history.php"><span>My History</span></a>
                    <?php if ($_SESSION["active-user"] === 'admin@admin.com') { ?>
                    <a href="admin-page.php" id="contacts"><span>Contacts</span></a>
                    <?php } ?>
                    <a href="logout.php"><span id="logout">Log-out</span></a>
                </div>
            </div>
            <h3>My History</h3>
        </header>

        <article>
            <form class="search-form" action="" method="post">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col" class="sorting-choice">Item</th>
                            <th scope="col" class="sorting-choice">Price / Store </th>
                            <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($historyList as $i => $item): ?>
                            <tr>
                            <?php if ($item['Prod_title'] != '--'){ ?>
                                <th> 
                                    <?php if (substr($previousDate,0,10) === substr($item['Record_date'],0,10)) {?>
                                        <span></span>
                                    <?php } else { ?>
                                    <?php echo substr($item['Record_date'],0,10); $previousDate = $item['Record_date']; } ?>
                                </th>
                                <td><?php echo $item['Prod_title'] ?></td>
                                <td class="datetime"><?php echo $item['Prod_price'].' EUR / '.$item['Store_Name'] ?></td>
                                <td><?php echo $item['Prod_amount']?></td>
                            <?php } ?>
                            </tr>
                        <?php if ($item['Prod_title'] === '--') { ?>
                            <tr ><td></td><td></td><td>Total Cost:</td><td><?php echo $item['Prod_price'].'EUR' ?></td></tr>
                            <tr class="gap-row"><td></td><td></td><td></td><td></td></tr>
                            <?php $previousDate = ''; ?>
                        <?php }?>
                        <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            <footer>                
        </article>        
    </aside>
</body>
<script src="myscripts.js"></script>
</html>