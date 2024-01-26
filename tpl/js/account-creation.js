// console.log("Hello world!");

function validateUsername(status = false) {
    let usernameField = document.getElementById("username");
    let usernameStatus = document.getElementById("username-status");

    // Only alphanumeric characters allowed
    let usernameRegex = /^[a-zA-Z0-9]{8,32}$/;

    let validUsername = usernameField.value.match(usernameRegex);

    if (validUsername == null) {
        if (status) {
            usernameStatus.innerHTML = "Please enter a username between 8 - 32 characters with only letters and numbers.";
            usernameField.focus();
        }
        return false;
    } else {
        usernameStatus.innerHTML = "";
        return true;
    }
}

function validatePassword(status = false) {
    let passwordField = document.getElementById("password");
    let passwordStatus = document.getElementById("password-status");
    
    // 8-32 letters, numbers and symbols allowed with at least 1 number and 1 symbol
    let passwordRegex = /^(?=.*[\d])(?=.*[!@#$%^&*])[\w!@#$%^&*]{8,32}$/;
    
    let validPassword = passwordField.value.match(passwordRegex);

    if (!matchPassword(status)) {
        return false;
    } else if (validPassword == null) {
        if (status) {
            passwordStatus.innerHTML = "Please enter a username between 8 - 32 characters with at least 1 number and 1 symbol.";
            passwordField.focus();
        }
        return false;
    } else {
        passwordStatus.innerHTML = "";
        return true;
    }
}

function matchPassword(status = false) {
    let passwordField = document.getElementById("password");
    let reenterField = document.getElementById("reenter-password");
    let reenterStatus = document.getElementById("reenter-password-status");

    if (reenterField.value.length == 0) {
        reenterStatus.innerHTML = '';
        return;
    } else if (passwordField.value != reenterField.value) {
        reenterStatus.style.color = "Red";
        reenterStatus.innerHTML = "Passwords do NOT match!"
        if (status) {
            reenterField.focus();
        }
        return false;
    } else {
        reenterStatus.style.color = "Green";
        reenterStatus.innerHTML = "Passwords match!"
        return true;
    }
}

/**
 * Validates form on submit
 */
function validateForm() {
    /** Can simplify to a one-liner, but an expanded if/else tree is more comprehensible and easier
     *  to add additional validations
     */ 
    if (validateUsername(true) && validatePassword(true)) {
        return true;
    } else {
        return false;
    }
}