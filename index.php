<?php

require_once "DBconnect.php";

session_start();

$myConnection = new MagebitTask();
$pdo = $myConnection -> connect();

$statement = $pdo->prepare('CREATE TABLE IF NOT EXISTS emails
(
    id		    int     AUTO_INCREMENT  NOT NULL,
	email	    varchar(50) 			NOT NULL,
	pass        varchar(50) 			NOT NULL,
	code	    varchar(20)				NOT NULL,
    datetime    datetime                NOT NULL,
    PRIMARY KEY (id)
)');
$statement -> execute();

$statement1 = $pdo->prepare('CREATE TABLE IF NOT EXISTS STORES
(
    STORE_ID    int     AUTO_INCREMENT  NOT NULL,
	Store_Name  varchar(50) 			NOT NULL,
    PRIMARY KEY (STORE_ID)
)');
$statement1 -> execute();

$statement = $pdo -> prepare("SELECT id, email, pass FROM emails");
$statement -> execute();
$accounts = $statement -> fetchAll(PDO::FETCH_ASSOC);

//declaring empty variables that will be used on first load prior to any user-entry
$email = '';
$pass = '';
$passConf = '';
$emailError = '';
$checkbox = '';
$checkboxError = '';
$duplicateAccountError = 0;
$loginConf = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginConf = trim($_POST['login-conf']);
    if ($loginConf == 'registration') {
        $duplicateAccountError = 0;
        $emailError = '';

    //with trim() function even if user accidentally entered (copy-pasted) email with 
    //space[s] at the end - php will still accept and corretly process the record
        $email = trim($_POST['email1']);
        $pass = trim($_POST['pass']);
        $passConf = trim($_POST['passConf']);

        foreach ($accounts as $account):
            if ($account['email'] === $email) { $duplicateAccountError = 1; }
        endforeach;

    //validating if no email has been provided/entered
        if (!$email) {
            $emailError = 'Email address is required';
        if (!$pass) {
            $passError = 'Password is required';
        }

    //using php function 'filter_var()' to validate email input
        } else if ($duplicateAccountError == 1) {
            $emailError = 'Account already exists. Please login or register new email.';
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
            //$checkbox = 'checked';
        }

    //only if no errors registered proceeding with data intry into database table
        if (!$emailError && !$checkboxError) {
            $code = substr($email, strpos($email, '@') + 1);
            $dateTime = date('Y-m-d H:i:s');

            $statement = $pdo->prepare("INSERT INTO emails (email, pass, code, datetime)
                            VALUES (:email, :pass, :code, :dateTime)");

            $statement->execute([
                ':email' => $email,
                ':pass' => $pass,
                ':code' => $code,
                ':dateTime' => $dateTime
            ]);
            $_SESSION["active-user"] = $email;
            header('Location: success-page.php');
        }    
    }
    else if ($loginConf == 'login') {
        $email = trim($_POST['email1']);
        $passConf = trim($_POST['passConf']);

        foreach ($accounts as $account):
            if ($account['email'] === $email) { 
                if ($account['pass'] != $passConf) {
                    $emailError = 'Password does not match'; 
                }
             }
        endforeach;

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
            //$checkbox = 'checked';
        }

    //only if no errors registered proceeding with data intry into database table
        if (!$emailError && !$checkboxError) {
            $code = substr($email, strpos($email, '@') + 1);
            $dateTime = date('Y-m-d H:i:s');
            $_SESSION["active-user"] = $email;
            header('Location: grocery-list.php');
        }    
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
    <title>Price Compare - SE RTU</title>
</head>

<body>
    <aside>
        <header>

<!-- added anchor tags to logo and 'How it works' for links to home page and admin page -->
            <a href="index.php" id="p-logo"></a>
            <a href="index.php" id="p-label"></a>
            
            <div id="header-links">
                <a href="grocery-list.php"><span>My Grocery List</span></a>
                <a href="#"><span>How this works</span></a>
                <a href="#"><span>Contacts</span></a>
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
            <?php if (!$email && !$_SESSION["active-user"]) {?>
                <div id="welcome">
                    <a href=""><div id="login" onmousedown="loginUser()">LOGIN</div></a>
                    <a href=""><div id="register" onmousedown="registerUser()">REGISTER</div></a>
            <?php } else if (!$email && $_SESSION["active-user"]){ ?>
                <div id="welcome">
                <a href="logout.php"><div>LOG-OUT</div></a>
            <?php }else { ?>
                <div id="welcome" class="hidden" >
            <?php } ?> 
            </div>            
            <?php if (!$email) {?>
                <div id="registration-fields" class="hidden">
            <?php } else { ?>                
                <div id="registration-fields">
            <?php } ?>             
            <?php if (!$passConf) {?>
                <h3>Create an account</h3>
                <h5>Register your new account to get access to beast deals in the city. Or <a href="#" onmousedown="loginUser()"><span><b>login</b></span></a> with the existing account</h5>
                <form id="submit-form" action="" method="post" name="form1">
                    
                    <input id="login-conf" class="hidden" type="text" name="login-conf" value="<?php echo $loginConf ?>">
                    <input id="email-input" type="text" placeholder="Type your email address here…" 
                    name="email1" value="<?php echo $email ?>">
                    <div id="password-input-parent">
                        <input id="password-input" type="password" placeholder="Choose your password…" 
                        name="pass" value="<?php echo $pass ?>">
                    </div>
            <?php } else { ?>
                <h3>Enter your account details</h3>
                <h5>Please enter your registered email and password</h5>
                <form id="submit-form" action="" method="post" name="form1">                    
                    <input id="login-conf" class="hidden" type="text" name="login-conf" value="<?php echo $loginConf ?>">
                    <input id="email-input" type="text" placeholder="Type your email address here…" 
                    name="email1" value="<?php echo $email ?>">
            <?php } ?>
                    <div id="password-verify-parent">
                        <input id="password-verify" type="password" placeholder="Re-enter chosen password…" 
                        name="passConf" value="<?php echo $pass ?>">                   
                        <button id="submit-button" type="submit" name="submit" value="Submit"></button>
                        <div id="pretend-button" class="hidden"></div>
                    </div>
                    
                    <span id="email-input-hover-text">email </span>
                    <div class="checkbox">
                        <input type="checkbox" id="email-checkbox" name="checkbox" <?php echo $checkbox ?>>
                        <label for="email-checkbox">I agree to <a href="#">the terms of service</a></label>
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
                </form>
            </div>
            <footer>
                <div id="line"></div>
                <div id="all-icons" class="all-icons">
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