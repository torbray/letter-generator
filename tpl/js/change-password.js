console.log("Hello world!");

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
    if (validatePassword(true)) {
        return true;
    } else {
        return false;
    }
}