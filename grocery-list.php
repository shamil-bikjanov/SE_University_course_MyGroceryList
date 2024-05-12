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

if (!$_SESSION["active-user"]) {
    header('Location: index.php');
}

//
$statement = $pdo -> prepare("SELECT id, email, pass FROM emails");
$statement -> execute();
$accounts = $statement -> fetchAll(PDO::FETCH_ASSOC);

foreach ($accounts as $account):
    if ($_SESSION["active-user"] === $account['email']) {
        $_SESSION["active-userID"] = $account['id'];
    }
endforeach;

$groceryListTitle = $_SESSION["active-userID"].'MyGroceryList';

// PRODUCTS TABLE
$statement = $pdo->prepare("CREATE TABLE IF NOT EXISTS PRODUCTS
                (
                    PRODUCT_ID	    int				NOT NULL AUTO_INCREMENT,
                    Prod_category   varchar(50)		NOT NULL,
                    Prod_title      varchar(50)		NOT NULL,
                    PRIMARY KEY (PRODUCT_ID),
                    constraint uc_Prod_Name unique (Prod_category,Prod_title)
                )");
$statement -> execute();

// MyGroceryList TABLE
$statement = $pdo->prepare("CREATE TABLE IF NOT EXISTS $groceryListTitle
                (
                    USER_ID	        int	    NOT NULL,
                    PRODUCT_ID      int		NOT NULL AUTO_INCREMENT,
                    Alert_Active    int		NOT NULL,
                    PRIMARY KEY (PRODUCT_ID)
                )");
$statement -> execute();

// STORES TABLE
$statement = $pdo->prepare("CREATE TABLE IF NOT EXISTS STORES
                (
                    STORE_ID	    int				NOT NULL AUTO_INCREMENT,
                    Store_Name 	    varchar(50)		NOT NULL,
                    selectPosition  int             NULL, 
                    PRIMARY KEY (STORE_ID),
                    constraint uc_Store_Name unique (Store_Name)
                )");
$statement -> execute();

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

$statement = $pdo -> prepare("  SELECT PRICES.PRODUCT_ID, PRICES.STORE_ID, PRICES.PROD_price, STORES.Store_Name
                                FROM PRICES
                                INNER JOIN STORES ON STORES.STORE_ID = PRICES.STORE_ID");
$statement -> execute();
$prices = $statement -> fetchAll(PDO::FETCH_ASSOC);

// CATEGORIES TABLE AND QUERY
$statement = $pdo -> prepare("SELECT distinct Prod_category FROM PRODUCTS ORDER BY Prod_category");
$statement -> execute();
$categories = $statement -> fetchAll(PDO::FETCH_ASSOC);

// PRODUCTS TABLE AND QUERY
$statement = $pdo -> prepare("  SELECT DISTINCT prod.PRODUCT_ID PRODUCT_ID, prod.Prod_category Prod_category, prod.Prod_title Prod_title, groc.Alert_Active Alert_Active
                                FROM $groceryListTitle groc
                                INNER JOIN PRODUCTS as prod ON groc.PRODUCT_ID = prod.PRODUCT_ID
                                ORDER BY prod.Prod_category, prod.Prod_title");
$statement -> execute();
$items = $statement -> fetchAll(PDO::FETCH_ASSOC);

$statement = $pdo -> prepare("SELECT * FROM PRODUCTS ORDER BY Prod_title");
$statement -> execute();
$products = $statement -> fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedCategory = $_POST['categorySelection'];

        if (!$selectedCategory) {
            $statement = $pdo -> prepare("SELECT * FROM PRODUCTS ORDER BY PRODUCT_ID");
        } else  {
            $statement = $pdo -> prepare("SELECT * FROM PRODUCTS WHERE Prod_category = $selectedCategory");
        if (!$selectedProduct)
            $selectedProduct = $_POST['items'];
        }

        if ($selectedCategory && $selectedProduct) {
            foreach($items as $item):
                if ($item['Prod_title'] === $selectedProduct) {
                    $selectedProduct = '';
                }
            endforeach;
            
            if ($selectedProduct) {
                $statement = $pdo->prepare("INSERT INTO $groceryListTitle (USER_ID, PRODUCT_ID, Alert_Active)
                            VALUES (1, (SELECT PRODUCT_ID FROM PRODUCTS WHERE Prod_title = :selectedProduct), 0)");

                $statement->execute([':selectedProduct' => $selectedProduct ]);
            }            
        }
        
        
            header('Location: grocery-list.php');
        }    
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin-styles.css">
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
                    <a href="my-history.php"><span>My History</span></a>
                    <?php if ($_SESSION["active-user"] === 'admin@admin.com') { ?>
                    <a href="admin-page.php" id="contacts"><span>Contacts</span></a>
                    <?php } ?>
                    <a href="logout.php"><span id="logout">Log-out</span></a>
                </div>
            </div>
            <h3>My Grocery list</h3>
        </header>
        
        <article>
            <form class="search-form" action="" method="post">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Category <?php echo $groceryListTitle ?></th>
                            <th scope="col" class="sorting-choice">Item</th>
                            <th scope="col" class="sorting-choice">Lowest price / Store </th>
                            <th scope="col">Alert active?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $i => $item): ?>
                                
                            <tr>
                                <th> <?php echo $item['Prod_category'] ?></th>
                                <td><?php echo $item['Prod_title'] ?></td>
                                <td class="datetime">
                                    <?php foreach($prices as $i => $price): ?>
                                    <?php if ($item['PRODUCT_ID'] == $price['PRODUCT_ID']){?>
                                        <?php if (!$lowestPrice){?>
                                            <?php $lowestPrice = $price['PROD_price']; $lowestPriceStore = $price['Store_Name']; ?>
                                        <?php } else if ($lowestPrice > $price['PROD_price']) { ?>
                                            <?php $lowestPrice = $price['PROD_price']; $lowestPriceStore = $price['Store_Name']; ?>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php endforeach; ?>
                                    <?php if ($lowestPrice != ''){?>
                                    <?php echo $lowestPrice; echo ' EUR / '; echo $lowestPriceStore; ?>
                                    <?php } ?>
                                    <?php $lowestPrice = ''; $lowestPriceStore = ''; ?>
                                </td>
                                <td>
                                <?php if ($item['Alert_Active'] == 1){?>
                                    <input type="checkbox" class="alert-checkbox" name="<?php echo 'item'.$item['PRODUCT_ID']?>" value="<?php echo $item['Alert_Active']?>" <?php echo 'checked'?>>
                                <?php } else {?>                                    
                                    <input type="checkbox" class="alert-checkbox" name="<?php echo 'item'.$item['PRODUCT_ID']?>" value="<?php echo $item['Alert_Active']?>">
                                <?php } ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                        <tr>
                            <td> 
                                <select id="categories" name='categorySelection' onchange="updateItems()"> 
                                <?php if($selectedCategory == ''){ ?>
                                    <option value="null">Select the category</option>
                                <?php } ?>
                                    <?php foreach($categories as $category): ?>                                        
                                        <option id="selCat" value="<?php echo $category['Prod_category'] ?>"><?php echo $category['Prod_category'] ?></option>                                        
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select id="items" name='items'>
                                </select>
                            </td>
                            <td> <!--
                                <span id="changed">
                                    <?php echo 'selCat = ['; echo $selectedCategory; echo '] , selProd = '; echo $selectedProduct; ?>
                                </span> -->
                            </td>
                            <td>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                <form class="delete-form" method="post" action="grocery-list.php">
                    <button type="sumbit" class="delete-button">Add item</button>
                </form>
                <form class="delete-form" method="post" action="deleteItem.php">
                    <input type="hidden" name="prodID" value="41">
                    <button type="sumbit" class="delete-button">Clear My Grocery List</button>
                </form>
                <div></div>   
                <form class="delete-form" method="post" action="my-history.php">
                    <input type="hidden" name="prodID1" value="<?php echo '12' ?>">
                    <button type="sumbit" class="delete-button">Add to My History</button>
                </form>     
            <footer>                
        </article>        
    </aside>
</body>
<script src="myscripts.js"></script>
</html>