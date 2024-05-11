This website has been created as a test task for Junior Developer position posted by Magebit Latvia.
Throughout the code comments are added for easier understanding and navigation

The website features:
- user accessible pages:
* landing/homepage ("index.php") where a simple form is displayed for email address input;
* success page ("success-page.php") - user is being redirected after successfully adding him email to the database (after passing required validations *);
* Admin page ("admin-page.php") - a page where emails are displayed as per Task-3 (PHP and MySQL) description and can be sorted;

- service only page (non-accessible by users):
* Delete page ("delete.php") - is utilized to process single-entry delete requests from Admin page.

- service files:
* DBconnectSetup.php - uses OOP principles for database connection design;
* DBconnect.php - creates an instance of 'DBconnect' class from previous file to setup connection to an actual database;
* myscripts.js - JavaScript code for Task-2;
* styles.css - main file with CSS styles;
* admin-styles.css - additional file with CSS styles specific for Admin page only.

1. Landing page ("index.php").

MySQL database ('magebit') and the only required table ('emails') has been created separately using PuTTY, however extra MySQL query is added to create the table if it doesn't exist.

In order to facilitate access links for homepage and Admin page added anchor files to 'pineapple' logo and 'How it works' header elements

Success page after full validation is passed is accessible in two different ways:
- if JavaScript is enabled - JS code is changing content of <h3>, <h5> tags, hides the form and displays 'trophy' logo
- if JavaScript is disabled - PHP code re-directs user to 'success-page.php' after validation is passed and form submitted.

Hidden by default <div id="pretend-button"> is used only within JS code user input validation logic.

<div> elements labelled error1, error2, error3, error4 exist from the first time loading of the page and are used with JS code.
Form validation with PHP is done in a conceptually different way when similar <div> elements are only generated when they are required.

2. Success page ("success-page.php").

Simple as a carrot. It is only used to display 'congrats' message after PHP validation is passed and data is submitted.

3. Admin page ("admin-page.php").

In order to manage a) search, b) sorting options, c) filter-buttons and d) sorting order (extra step) - relevant variables are introduced.

Comments are used within code for easy understanding and navigation.

All sorting options except 'search' field are utilizing 'radio' unput elements. 
All 'input' elements are placed within same <form> tag to ensure that all can be used at the same time as per task requirements. 
All 'input' elements 'remember' user choices to avoid hassle of unnecessary repetitions (except 'Delete' button function, since it is followed by a new query to database).


Table with emails display is generated using PHP 'foreach' and each iteration creates a separate simple 'form' element for every 'Delete' button.

4. Delete page ("delete.php").

Simple algorithm of deleting a single entry from database based on the obtained 'Primary Key'. Redirects to Admin page immediately after.


5. DBconnectSetup.php - uses OOP principles for database connection design;

6. DBconnect.php.

Four key attributes: 'port' , 'DBname', 'username' and 'password' are to be updated in order to establish connection to a database as required.

7. myscripts.js.

JavaScript code for Task-2. Additionally to comments within the code one section (a separate function) is commented out since it was designed to display 'Success' message at the end of Task-2. While within Task-3 validation and 'Success' message is handled using PHP - this block of JS code is redundant.

8. styles.css - main file with CSS styles, including adaptation for mobile devices (screen resolution less than 500px wide).

9. admin-styles.css - additional file with CSS styles specific for Admin page only. No adaptation for mobile devices has been done (only Desktop version).
