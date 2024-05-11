<?php

require_once "DBconnect.php";

$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();

$statement = $pdo->prepare('CREATE TABLE IF NOT EXISTS emails
(
    id		    int     AUTO_INCREMENT  NOT NULL,
	email	    varchar(50) 			NOT NULL,
	code	    varchar(20)				NOT NULL,
    datetime    datetime                NOT NULL,
    PRIMARY KEY (id)
)');
$statement -> execute();

//declaring empty variables that will be used on first load prior to any user-entry
$email = '';
$emailError = '';
$checkbox = '';
$checkboxError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

//with trim() function even if user accidentally entered (copy-pasted) email with 
//space[s] at the end - php will still accept and corretly process the record
    $email = trim($_POST['email1']);

//validating if no email has been provided/entered
    if (!$email) {
        $emailError = 'Email address is required';

//using php function 'filter_var()' to validate email input
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'Please provide a valid email address';

//combination of php-functions 'strtpos()' and 'substr()' to check if email ends with '.co'
    } else if (substr($email, strrpos($email, '.') + 1) == 'co') {
        $emailError = 'We are not accepting subscriptions from Colombia emails';
    }

//validating if user clicked the checkbox using php-function 'isset()'
    if (!isset($_POST['checkbox'])) {
        $checkboxError = 'You must accept the terms and conditions';
    } else {
        $checkbox = 'checked';
    }

//only if no errors registered proceeding with data intry into database table
    if (!$emailError && !$checkboxError) {
        $code = substr($email, strpos($email, '@') + 1);
        $dateTime = date('Y-m-d H:i:s');

        $statement = $pdo->prepare("INSERT INTO emails (email, code, datetime)
                        VALUES (:email, :code, :dateTime)");

/* 
//using different methods to see the best-fit choice
        $statement->bindValue(':email', $email);
        $statement->bindValue(':code', $code);
        $statement->bindValue(':dateTime', $dateTime);
        $statement->execute();
*/

        $statement->execute([
            ':email' => $email,
            ':code' => $code,
            ':dateTime' => $dateTime
        ]);
        header('Location: success-page.php');
    }    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Magebit task</title>
</head>

<body>
    <aside>
        <header>

<!-- added anchor tags to logo and 'How it works' for links to home page and admin page -->
            <a href="index.php" id="p-logo"></a>
            <a href="index.php" id="p-label"></a>
            
            <div id="header-links">
                <a href="#"><span>About</span></a>
                <a href="admin-page.php"><span>How it works</span></a>
                <a href="#"><span>Contact</span></a>
            </div>
        </header>
        
        <article>
            <div id="success-logo" class="hidden">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <h3>Subscribe to newsletter</h3>
            <h5>Subscribe to our newsletter and get 10% discount on pineapple glasses.</h5>
            <form id="submit-form" action="" method="post" name="form1">
                <div id="pretend-button" class="hidden"></div>

                <input id="email-input" type="text" placeholder="Type your email address here…" 
                name="email1" value="<?php echo $email ?>">                
                <button id="submit-button" type="submit" name="submit" value="Submit"></button>
                
                <span id="email-input-hover-text">email </span>
                <div class="checkbox">
                    <input type="checkbox" id="email-checkbox" name="checkbox" <?php echo $checkbox ?>>
                    <label for="email-checkbox">I agree to <a href="#">terms of service</a></label>
                </div>
                    <?php if ($emailError) :?>
                        <div class="visible error error1"><?php echo $emailError ?></div>
                    <?php endif; ?>
                    <?php if ($checkboxError) :?>
                        <div class="visible error error3"><?php echo $checkboxError ?></div>
                    <?php endif; ?>
                    <div id="error1" name="error1" class="hidden error error1">Please provide a valid email address</div>
                    <div id="error2" name="error2" class="hidden error error2">Email address is required</div>
                    <div id="error3" name="error3" class="hidden error error3">You must accept the terms and conditions</div>
                    <div id="error4" name="error4" class="hidden error error4">We are not accepting subscriptions from Colombia emails</div>
                </div>
                
            </form>
            <footer>
                <div id="line"></div>
                <div class="all-icons">
                    <a href="#" class="icon icon-fb">
                        <div >
                        </div>
                    </a>
                    <a href="#" class="icon icon-ig">
                        <div >
                        </div>
                    </a>
                    <a href="#" class="icon icon-tw">
                        <div >
                        </div>
                    </a>
                    <a href="#" class="icon icon-yt">
                        <div >
                        </div>
                    </a>
                </div>
            </footer>
        </article>
    </aside>
    <section>
    </section>
</body>
<script src="myscripts.js"></script>
</html>