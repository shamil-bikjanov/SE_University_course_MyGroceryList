<?php

require_once "DBconnect.php";

$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin-styles.css">
    <title>Admin Page</title>
</head>

<body>
    <aside>
        <header>
            <a href="index.php" id="p-logo"></a>
            <a href="index.php" id="p-label"></a>

            <div id="header-links">
                <a href="grocery-list.php"><span>My Grocery List</span></a>
                <a href="quick-compare.php"><span>Quick compare</span></a>
                <a href="#"><span>Contacts</span></a>
            </div>
        </header>
        
        <article>
            <form class="search-form" action="">
                <h3>Registered emails</h3>
                <div id="filter-buttons">

                    <?php foreach ($uniqueCodes as $code): ?>
                        <div>
                            <input type="radio" id="<?php echo $code ?>" name="filter-button" 
                            value="<?php echo $code ?>" 
                            <?php if ($code == $filterButton) {echo 'checked';} ?>>                
                            <label for="<?php echo $code ?>"><?php echo $code ?></label>
                        </div>
                    <?php endforeach; ?>

                    <div>
                        <input type="radio" id="none" name="filter-button" 
                        value="">                
                        <label for="none">no filter</label>
                    </div>
                </div>
                          
                <div class="input-search">
                    <input type="text" class="form-control" placeholder="Search for emails" name="search" value="<?php echo $search ?>">
                    <div class="input-group-button">
                        <button class="search-button" type="submit">Search</button>
                    </div>
                    <div class="order-by-container">
                    <input type="radio" id="ASC" name="order" 

                        value="ASC" <?php if ($sortOrder == 'ASC') {echo 'checked';} ?>>

                    <label for="ASC">ASC</label><br>
                    <input type="radio" id="DESC" name="order" 

                        value="DESC" <?php if ($sortOrder == 'DESC') {echo 'checked';} ?>>  

                    <label for="DESC">DESC</label><br>
                    <span class="tooltiptext">order: Accending / Descending</span>
                </div> 
                </div> 
            
                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col" class="sorting-choice"><label for="email">Email</label>
                            <input type="radio" id="email" name="sort-by" class="sorting-by"

                                    value="email" <?php if ($sortBy == 'email') {echo 'checked';} ?>>
                        </th>
                        <th scope="col" class="sorting-choice"><label for="datetime">Created:</label>
                            <input type="radio" id="datetime" name="sort-by" class="sorting-by"

                                    value="datetime" <?php if ($sortBy == 'datetime') {echo 'checked';} ?>>
                        </th>
                        <th scope="col">Action</th>
                        </tr>
                    </thead>
            </form>
                    <tbody>

                        <?php foreach($emails as $i => $email): ?>
                            
                            <tr>
                                <th> <?php echo $i + 1 ?></th>
                                <td><?php echo $email['email'] ?></td>
                                <td class="datetime"><?php echo $email['datetime'] ?></td>
                                <td>
                                    <form class="delete-form" method="post" action="delete.php">
                                        <input type="hidden" name="id" value="<?php echo $email['id'] ?>">
                                        <button type="sumbit" class="delete-button">Delete</button>
                                    </form>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>
                </table>            
            <footer>                
        </article>        
    </aside>
</body>
<script src="myscripts.js"></script>
</html>
