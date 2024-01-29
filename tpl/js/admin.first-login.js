// console.log("Hello world!");
function validateInput(field, status, send_status = false, regex, error) {
    let validInput = field.value.match(regex);

    // If regex fails to match
    if (validInput == null) {
        if (send_status) {
            status.innerHTML = error;
            field.focus();
        }
        return false;
    } else {
        status.innerHTML = '';
        return true;
    }
}

function validateFirstName(send_status = false) {
    let firstNameField = document.getElementById("first-name");
    let firstNameStatus = document.getElementById("first-name-status");

    // Only alphanumeric characters allowed
    let firstNameRegex = /^[a-zA-Z-\s]{1,64}$/;

    return validateInput(
        field = firstNameField,
        status = firstNameStatus,
        send_status = send_status,
        regex = firstNameRegex,
        error = "Only letters, space and dashes are allowed."
    )
}

function validateLastName(send_status = false) {
    let lastNameField = document.getElementById("last-name");
    let lastNameStatus = document.getElementById("last-name-status");

    // Only alphanumeric characters allowed
    let lastNameRegex = /^[a-zA-Z-\s]{1,64}$/;

    return validateInput(
        field = lastNameField,
        status = lastNameStatus,
        send_status = send_status,
        regex = lastNameRegex,
        error = "Only letters, space and dashes are allowed."
    )
}

function validateUsername(send_status = false) {
    let usernameField = document.getElementById("username");
    let usernameStatus = document.getElementById("username-status");

    // Only alphanumeric characters and underscores allowed
    let usernameRegex = /^[a-zA-Z0-9_]{3,32}$/;

    return validateInput(
        field = usernameField,
        status = usernameStatus,
        send_status = send_status,
        regex = usernameRegex,
        error = "Please enter a username between 8 - 32 characters with only letters, numbers and underscores."
    )    
}

function validatePassword(send_status = false) {
    let passwordField = document.getElementById("password");
    let passwordStatus = document.getElementById("password-status");
    
    // 8-32 letters, numbers and symbols allowed with at least 1 number and 1 symbol
    let passwordRegex = /^(?=.*[\d])(?=.*[!@#$%^&*])[\w!@#$%^&*]{8,32}$/;

    if (!matchPassword(send_status)) {
        return false;
    } 
    
    return validateInput(
        field = passwordField,
        status = passwordStatus,
        send_status = send_status,
        regex = passwordRegex,
        error = "Please enter a username between 8 - 32 characters with at least 1 number and 1 symbol."
    )   
}

function matchPassword(send_status = false) {
    let passwordField = document.getElementById("password");
    let reenterField = document.getElementById("reenter-password");
    let reenterStatus = document.getElementById("reenter-password-status");

    // Don't display mismatching password if nothing entered into re-enter password
    if (reenterField.value.length == 0) {
        reenterStatus.innerHTML = '';
        return;
    } else if (passwordField.value != reenterField.value) {
        // if passwords don't match
        reenterStatus.style.color = "Red";
        reenterStatus.innerHTML = "Passwords do NOT match!"
        if (send_status) {
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
    if (validateFirstName(true) && validateLastName(true) && validateUsername(true) && validatePassword(true)) {
        return true;
    } else {
        return false;
    }
}