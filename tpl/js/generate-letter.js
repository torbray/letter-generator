function confirmLetter() {
    let query = "Are you sure you want to generate a letter?"

    let answer = window.confirm(query);

    if (answer) {
        // Get variables from form inputs
        let fullName = document.querySelector('input[name="customer-name-full"]').value;
        let password = document.getElementById("letter-password").value;

        // Template literal
        let stmt = `Letter generated for: ${fullName}\n\nPassword:`;

        // Send window alert
        let prompt = window.prompt(stmt, password);

        if (prompt) {
            return true;
        }
    }

    return false;
}