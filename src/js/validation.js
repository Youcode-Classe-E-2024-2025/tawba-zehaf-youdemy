function showModal(message) {
    document.getElementById('errorMessage').innerText = message;
    document.getElementById('errorModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('errorModal').style.display = 'none';
}

// Update the validate functions
function validateLoginForm() {
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    if (!validateEmail(email)) {
        showModal('Please enter a valid email address.');
        return false;
    }

    if (!validatePassword(password)) {
        showModal('Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.');
        return false;
    }

    return true; // Form is valid
}

function validateRegistrationForm() {
    const username = document.getElementById('register-username').value;
    const email = document.getElementById('register-email').value;
    const password = document.getElementById('register-password').value;

    if (!validateUsername(username)) {
        showModal('Username must be 3-15 characters long and can only contain letters, numbers, and underscores.');
        return false;
    }

    if (!validateEmail(email)) {
        showModal('Please enter a valid email address.');
        return false;
    }

    if (!validatePassword(password)) {
        showModal('Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.');
        return false;
    }

    return true; // Form is valid
}