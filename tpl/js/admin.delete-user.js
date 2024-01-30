// console.log("Hello world!");

/**
 * Validates form on submit
 */
function validateForm() {
    let firstName = document.getElementById("first-name").innerHTML;
    let lastName = document.getElementById("last-name").innerHTML;

    let question = `Are you sure you want to delete ${firstName} ${lastName}'s profile?`;

    let answer = window.confirm(question);

    // Return Yes or No confirmation dialog - fail if user clicks no
    return answer;

}