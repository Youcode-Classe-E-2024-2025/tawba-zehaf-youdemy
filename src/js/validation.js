
function validateForm(username, email, password, confirmPassword) {
    let isValid = true;

    // Username validation
    if (username.length < 3) {
        showError('Username must be at least 3 characters');
        isValid = false;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('Please enter a valid email address§');
        isValid = false;
    }

    // Password validation
    if (password.length < 8) {
        showError('Password must be at least 8 characters');
        isValid = false;
    }

    // Confirm password
    if (password !== confirmPassword) {
        showError('Passwords do not match');
        isValid = false;
    }

    return isValid;
}


function validateLoginForm() {
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    if (!email || !password) {
        showError('Please fill in all fields');
        return false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('Please enter a valid email address');
        return false;
    }

    return true;
}


