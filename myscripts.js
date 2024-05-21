const email1 = document.querySelector('#email-input');
const checkbox = document.querySelector('#email-checkbox');
const error1 = document.querySelector('#error1'); // valid email is provided
const error2 = document.querySelector('#error2'); // email field is NOT blank
const error3 = document.querySelector('#error3'); // T&C are accepted (checkmark)
const button1 = document.querySelector('#submit-button');
const welcomeSection = document.querySelector('#welcome');
const registrationFields = document.querySelector('.registration-wrapper');
const registerButton = document.querySelector('#register');
const inputPassword = document.querySelector('#password-verify');
const passwordInputParent = document.querySelector('#password-input-parent');
const loginConf = document.querySelector('#login-conf');

var selectedCategory = document.querySelector('#categories');

var changedText = document.getElementById('changed');
//changedText.textContent = 'hallo';
//changedText.textContent = funString;
/*function listQ(){
    changedText.textContent = this.value;
}
document.getElementById("categories").onchange = listQ;*/



let products = [
    ['Fruits and Vegetables', 'Apple'],
    ['Fruits and Vegetables', 'Banana'],
    ['Fruits and Vegetables', 'Bell Pepper'],
    ['Fruits and Vegetables', 'Blueberry'],
    ['Fruits and Vegetables', 'Broccoli'],
    ['Fruits and Vegetables', 'Carrot'],
    ['Fruits and Vegetables', 'Cucumber'],
    ['Fruits and Vegetables', 'Grapes'],
    ['Fruits and Vegetables', 'Onion'],
    ['Fruits and Vegetables', 'Orange'],
    ['Fruits and Vegetables', 'Potato'],
    ['Fruits and Vegetables', 'Pears'],
    ['Fruits and Vegetables', 'Spinach'],
    ['Fruits and Vegetables', 'Strawberry'],
    ['Fruits and Vegetables', 'Tomato'],
    ['Fruits and Vegetables', 'Watermelon'],
    ['Dairy Products and Eggs', 'Milk (Whole, Skim, or 2%)'],
    ['Dairy Products and Eggs', 'Eggs (Grade A Large, Brown, or Organic)'],
    ['Dairy Products and Eggs', 'Butter (Salted or Unsalted)'],
    ['Dairy Products and Eggs', 'Cheese (Cheddar, Mozzarella, or Swiss)'],
    ['Dairy Products and Eggs', 'Yogurt (Plain, Greek, or Flavored)'],
    ['Dairy Products and Eggs', 'Cream (Heavy Cream or Half-and-Half)'],
    ['Dairy Products and Eggs', 'Sour Cream'],
    ['Dairy Products and Eggs', 'Cottage Cheese'],
    ['Dairy Products and Eggs', 'Whipped Cream'],
    ['Dairy Products and Eggs', 'Egg Whites (Carton)'],
    ['Meat and Seafood', 'Fresh Atlantic Salmon Fillets'],
    ['Meat and Seafood', 'Grass-fed Beef Ribeye Steaks'],
    ['Meat and Seafood', 'Organic Chicken Breast'],
    ['Meat and Seafood', 'Wild-caught Gulf Shrimp'],
    ['Meat and Seafood', 'Pork Tenderloin'],
    ['Meat and Seafood', 'Lamb Chops'],
    ['Meat and Seafood', 'Alaskan King Crab Legs'],
    ['Meat and Seafood', 'Ground Turkey'],
    ['Meat and Seafood', 'Swordfish Steaks'],
    ['Meat and Seafood', 'Italian Sausages'],
    ['Bakery and Baked Goods', 'Whole Wheat Bread'],
    ['Bakery and Baked Goods', 'Croissants'],
    ['Bakery and Baked Goods', 'Blueberry Muffins'],
    ['Bakery and Baked Goods', 'Bagels'],
    ['Bakery and Baked Goods', 'Chocolate Chip Cookies'],
    ['Bakery and Baked Goods', 'Cinnamon Rolls'],
    ['Bakery and Baked Goods', 'French Baguette'],
    ['Bakery and Baked Goods', 'Sourdough Bread'],
    ['Bakery and Baked Goods', 'Pita Bread'],
    ['Bakery and Baked Goods', 'Gluten-Free Brownies'],
    ['Beverages', 'Orange Juice'],
    ['Beverages', 'Green Tea'],
    ['Beverages', 'Cola'],
    ['Beverages', 'Almond Milk'],
    ['Beverages', 'Sparkling Water'],
    ['Canned and Packaged Foods', 'Canned Black Beans'],
    ['Canned and Packaged Foods', 'Tomato Sauce'],
    ['Canned and Packaged Foods', 'Chicken Noodle Soup'],
    ['Canned and Packaged Foods', 'Tuna in Water'],
    ['Canned and Packaged Foods', 'Instant Ramen Noodles']
];

//var newOption = new Option('Option Text','Option Value');
//document.querySelector('#contacts').onclick = alert('Please enter the name.');

var select = document.querySelector('#items'); 
var selCat = document.querySelector('#selCat');
var match = 0;

function updateItems(){
    select.textContent = null;
    for (var i in products) {
        if (selectedCategory['value'] === products[i][0]) {
            var newOption = new Option(products[i][1],products[i][1]);
            select.add(newOption,undefined);
            match = i;
            //changedText.textContent+=products[i][1];
        }
        
    }    
   // changedText.textContent = selectedCategory['value'];
}
function listQ(){
    changedText.textContent = products[0][1];
}


if(loginConf.value){
    if(loginConf.value === "login"){
        loginUser();
    }
    else if (loginConf.value === "registration"){
        registerUser();
    }
}

function registerUser() {
    registrationFields.classList.remove("hidden");    
    registrationFields.setAttribute('class', 'visible');
    welcomeSection.setAttribute('class', 'hidden');
    loginConf.value = "registration";
}
function loginUser() {
    document.querySelector('h3').innerHTML = "Enter your account details";
    document.querySelector('h5').innerHTML = "Please enter your registered email and password";
    registrationFields.classList.remove("hidden");    
    registrationFields.setAttribute('class', 'visible');
    welcomeSection.setAttribute('class', 'hidden');
    passwordInputParent.setAttribute('class', 'hidden');
    inputPassword.setAttribute('placeholder', 'Enter your password');
    loginConf.value = "login";
}

//document.getElementById("categories").onchange = listQ();

//document.getElementById("changed").innerHTML = JSON.stringify(array);


// using variable below as starting point to validate the entire form
var entryAttemptReceived = false;

//declaring default state of form submit button as 'disabled'
button1.disabled = true;

function validateEmailFormat(inputText) {
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,8})+(\s)*$/;
    if(inputText.value.match(mailformat)) {
        return false;
    } else {
        return true;
    }
}

function validateEmail() {
    return (!email1 || !email1.value);
}

//checking if valid (as per task conditions) email address is entered by user
email1.addEventListener('blur', errorsValidation);

// using 'pretend-button' element positioned on top of button to enable the use of 'click' EventListener
const pretendButton = document.querySelector('#pretend-button');
pretendButton.classList.remove("hidden");
pretendButton.setAttribute('class', 'visible');
pretendButton.addEventListener('click', function(){
    entryAttemptReceived = true;
    errorsValidation();
    termsAcceptValidation();
});

function errorsValidation() {
    if (email1.value || entryAttemptReceived) {
        if (validateEmail()) {
            showError(error2); 
        } else {
            hideError(error2);
        }
        
        if (!validateEmail() && validateEmailFormat(document.form1.email1)) {
            showError(error1);
        } else {
            hideError(error1);
        }
        
        entryAttemptReceived = true;
    }    
}

// ONLY after we confirm first attempt to submit the form we start validating email on user input
//...(prior to that - on submit-button click only)
email1.addEventListener('input', errorsValidationOnInput);
function errorsValidationOnInput() {
    if (entryAttemptReceived) {
        errorsValidation();
        enableButton();
    }    
}

// validating whether user accepted T&C [marked the checkbox]
checkbox.addEventListener('click', function(){
    entryAttemptReceived = true;
    errorsValidation();
    termsAcceptValidation();
// ...but only if first attempt to submit was done
// ... we also check whether submit-button must be enabled
    if (entryAttemptReceived) {
        enableButton();
    }
});

function termsAcceptValidation() {
    if (checkbox.checked) {
        hideError(error3);
    } else {
        showError(error3);
        checkbox.focus();
    }
}

function hideError(error) {
    error.classList.remove("visible");
    error.setAttribute('class', 'hidden');
    error.display = 'hidden'; //adding attribute to validate 'visibility' of this element
}

function showError(error) {
    error.classList.remove("hidden");
    if (error == error3) {
        error.setAttribute('class', 'visible error error3');
    } else {
        error.setAttribute('class', 'visible error error1');
    }
    error.display = 'visible'; //adding attribute to validate 'visibility' of this element
}

// function that checks if no errors are on display 
// ...and enables/disables submit button accordingly
function enableButton() {
    var errors = [error1, error2, error3];
    var noErrors = true;
    errors.forEach(function(error) {
        if (error.display == 'visible') {
            noErrors = false;
        }
    });
    if (noErrors) {
        pretendButton.classList.remove("visible");
        pretendButton.setAttribute('class', 'hidden');
        button1.disabled = false;
    } else {
        pretendButton.classList.remove("hidden");
        pretendButton.setAttribute('class', 'visible');
        button1.disabled = true;
    }
}
/*
 // final step for Task-2
document.querySelector('#submit-form').addEventListener('submit', function() {
    if (!button1.disabled) {
        //event.preventDefault();
        document.querySelector('#success-logo').classList.remove("hidden");
        document.querySelector('h3').innerHTML = "Thanks for subscribing!";
        document.querySelector('h5').innerHTML = "You have successfully subscribed to our email listing. Check your email for the discount code.";
        document.querySelector('form').setAttribute('class', 'hidden');
    }  
});
*/