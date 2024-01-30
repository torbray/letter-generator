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
        error = "Please enter a username between 3 - 32 characters with only letters, numbers and underscores."
    )    
}

/**
 * Validates form on submit
 */
function validateForm() {
    if (validateFirstName(true) && validateLastName(true) && validateUsername(true)) {
        let firstName = document.getElementById("first-name").value;
        let lastName = document.getElementById("last-name").value;

        let question = `Are you sure you want to add ${firstName} ${lastName} to the system?`;

        let answer = window.confirm(question);

        // Return Yes or No confirmation dialog - fail if user clicks no

        return answer;
    } else {
        return false;
    }
}