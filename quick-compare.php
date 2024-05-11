<?php

require_once "DBconnect.php";

$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();
/*
//declaring key initial values for page display,
//and getting user-selected choices during user interaction
$search = $_GET['search'] ?? '';
$sortBy = $_GET['sort-by'] ?? 'datetime';
$sortOrder = $_GET['order'] ?? 'ASC';
$filterButton = $_GET['filter-button'] ?? '';

//all content are extracted from database in order to filter unique ones (filter buttons)
$statement = $pdo -> prepare("SELECT code FROM emails ORDER BY $sortBy $sortOrder");
$statement -> execute();
$emails = $statement -> fetchAll(PDO::FETCH_ASSOC);

//creating single dimention array from obtained earlier associative array
$codesArray = array();
$i = 0;
foreach ($emails as $email) {
    $codesArray[$i] = $email['code'];
    $i++;
}
//using php-function array_unique() to have unique 'codes' for filter-buttons
$uniqueCodes = array_unique($codesArray);

//using four different sql queries based on 'filters' selected by the user
//such as 'search' field input and email domain based 'filter-buttons' 
if ($filterButton) {
    if ($search) {
        $statement = $pdo -> prepare(
            "   SELECT * 
                FROM emails 
                WHERE email LIKE :email 
                AND code = :code 
                ORDER BY $sortBy $sortOrder");
        $statement -> bindValue(':email', "%$search%");
        $statement -> bindValue(':code', $filterButton);        
    } else {
        $statement = $pdo -> prepare(
            "   SELECT * 
                FROM emails 
                WHERE code = :code
                ORDER BY $sortBy $sortOrder");
        $statement -> bindValue(':code', $filterButton); 
    }
} else {
    if ($search) {
        $statement = $pdo -> prepare("SELECT * FROM emails WHERE email LIKE :email ORDER BY $sortBy $sortOrder");
        $statement -> bindValue(':email', "%$search%");
    } else {
        $statement = $pdo -> prepare("SELECT * FROM emails ORDER BY $sortBy $sortOrder");
    }
}

$statement -> execute();
$emails = $statement -> fetchAll(PDO::FETCH_ASSOC);


*/

$store1 = '';
$store2 = '';
$store3 = '';
$testMessage = '';

// STORES TABLE AND QUERY
$statement = $pdo -> prepare("SELECT distinct STORE_ID, Store_Name FROM STORES ORDER BY Store_Name");
$statement -> execute();
$stores = $statement -> fetchAll(PDO::FETCH_ASSOC);

// PRICES TABLE AND QUERY
$statement = $pdo->prepare('CREATE TABLE IF NOT EXISTS PRICES
                (
                    PROD_price	    decimal(10,2) 	NOT NULL,
                    Price_datetime  datetime 		NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    STORE_ID	    int				NOT NULL,
                    PRODUCT_ID 	    int				NOT NULL, 
                    PRIMARY KEY (STORE_ID, PRODUCT_ID)
                )');
$statement -> execute();

$statement = $pdo -> prepare("  SELECT PRICES.PRODUCT_ID, PRICES.STORE_ID, PRICES.PROD_price, STORES.Store_Name, STORES.selectPosition
                                FROM PRICES
                                INNER JOIN STORES ON STORES.STORE_ID = PRICES.STORE_ID");
$statement -> execute();
$prices = $statement -> fetchAll(PDO::FETCH_ASSOC);

foreach($prices as $price):
    if ($price['selectPosition']) {
        if($price['selectPosition'] === 1) {
            $store1 = $price['Store_Name'];
        } 
        if ($price['selectPosition'] === 2) {        
            $store2 = $price['Store_Name'];
        } 
        if ($price['selectPosition'] === 3) {        
            $store3 = $price['Store_Name'];
    }
    }
endforeach;

// CATEGORIES TABLE AND QUERY
$statement = $pdo -> prepare("SELECT distinct Prod_category FROM PRODUCTS ORDER BY PRODUCT_ID");
$statement -> execute();
$categories = $statement -> fetchAll(PDO::FETCH_ASSOC);

// PRODUCTS TABLE AND QUERY
$statement = $pdo -> prepare("  SELECT DISTINCT prod.PRODUCT_ID PRODUCT_ID, prod.Prod_category Prod_category, prod.Prod_title Prod_title, groc.Alert_Active Alert_Active
                                FROM MyGroceryList groc
                                INNER JOIN PRODUCTS as prod ON groc.PRODUCT_ID = prod.PRODUCT_ID
                                ORDER BY prod.Prod_category, prod.Prod_title");
$statement -> execute();
$items = $statement -> fetchAll(PDO::FETCH_ASSOC);

$statement = $pdo -> prepare("SELECT * FROM PRODUCTS ORDER BY PRODUCT_ID");
$statement -> execute();
$products = $statement -> fetchAll(PDO::FETCH_ASSOC);
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store1 = $_POST['store1'];
    $store1 = $_POST['store2'];
    $store1 = $_POST['store3'];
        
        
        
            header('Location: quick-compare.php');
        }    
    
*/
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
            <div>
                <div id="icons">
                    <a href="index.php" id="p-logo"></a>
                    <a href="index.php" id="p-label"></a>
                </div>

                <div id="header-links">
                    <a href="grocery-list.php"><span>My Grocery List</span></a>
                    <a href="quick-compare.php"><span>Quick compare</span></a>
                    <a href="admin-page.php" id="contacts"><span>Contacts</span></a>
                </div>
            </div>
            <h3>Quick compare</h3>
        </header>
        
        <article>
            <form class="search-form" action="addItem.php" method="post">
                <table class="table">
                    <thead>
                        <tr>
                            <td></td>
                            <td> 
                                <select id="categories" name='store1'> 
                                <?php if($store1 == ''){ ?>
                                    <option value="null">Select the Store 1</option>
                                <?php }else{ ?>
                                    <option value="<?php echo $store1 ?>"> </option>
                                <?php } ?>
                                    <?php foreach($stores as $store): ?>                                        
                                        <option id="selCat" value="<?php echo $store['Store_Name'] ?>"><?php echo $store['Store_Name'] ?></option>                                        
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td> 
                                <select id="categories" name='store2'> 
                                <?php if($store2 == ''){ ?>
                                    <option value="null">Select the Store 2</option>
                                    <?php }else{ ?>
                                    <option value="<?php echo $store2 ?>"> </option>
                                <?php } ?>
                                    <?php foreach($stores as $store): ?>                                        
                                        <option id="selCat" value="<?php echo $store['Store_Name'] ?>"><?php echo $store['Store_Name'] ?></option>                                        
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td> 
                                <select id="categories" name='store3'> 
                                <?php if($store3 == ''){ ?>
                                    <option value="null">Select the Store 3</option>
                                    <?php }else{ ?>
                                    <option value="<?php echo $store3 ?>"> </option>
                                <?php } ?>
                                    <?php foreach($stores as $store): ?>                                        
                                        <option id="selCat" value="<?php echo $store['Store_Name'] ?>"><?php echo $store['Store_Name'] ?></option>                                        
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="col">Item</th>
                            <th scope="col">
                                <?php if($store1 == ''){ ?>
                                    --
                                <?php }else{ ?>
                                    <?php echo $store1 ?>
                                <?php } ?>
                            </th>
                            <th scope="col">
                                <?php if($store2 == ''){ ?>
                                    --
                                <?php }else{ ?>
                                    <?php echo $store2 ?>
                                <?php } ?>
                            </th>
                            <th scope="col">
                                <?php if($store3 == ''){ ?>
                                    --
                                <?php }else{ ?>
                                    <?php echo $store3 ?>
                                <?php } ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach($items as $i => $item): ?>
                                
                            <tr>
                                <td><b><?php echo $item['Prod_title'] ?></b></td>
                                <td class="datetime">
                                    <?php foreach($prices as $i => $price): ?>
                                        <?php if ($price['selectPosition'] == 1){?>
                                            <?php if ($item['PRODUCT_ID'] == $price['PRODUCT_ID']){?>
                                                <?php echo $price['PROD_price'].' EUR' ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                </td>
                                <td class="datetime">
                                    <?php foreach($prices as $i => $price): ?>
                                        <?php if ($price['selectPosition'] == 2){?>
                                            <?php if ($item['PRODUCT_ID'] == $price['PRODUCT_ID']){?>
                                                <?php echo $price['PROD_price'].' EUR' ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                </td><td class="datetime">
                                    <?php foreach($prices as $i => $price): ?>
                                        <?php if ($price['selectPosition'] == 3){?>
                                            <?php if ($item['PRODUCT_ID'] == $price['PRODUCT_ID']){?>
                                                <?php echo $price['PROD_price'].' EUR' ?>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                        
                    </tbody>
                </table>
                <button type="sumbit" class="delete-button">Refresh</button>
                </form>
                
                <div><?php echo 'stores = '.$store1.' // '.$store2.' // '.$store3 ?></div>  
                                            
            <footer>                
        </article>        
    </aside>
</body>
<script src="myscripts.js"></script>
</html>
