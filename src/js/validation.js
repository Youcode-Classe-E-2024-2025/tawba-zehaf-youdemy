
document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.querySelector('form');

    registerForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const username = document.getElementById('register-username').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm_password').value;

        // Clear previous errors
        hideAllErrors();

        if (validateForm(username, email, password, confirmPassword)) {
            this.submit();
        }
    });
});

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
        showError('Please enter a valid email address');
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

function showError(message) {
    const errorModal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.textContent = message;
    errorModal.classList.remove('hidden');
}

function hideAllErrors() {
    const errorModal = document.getElementById('errorModal');
    errorModal.classList.add('hidden');
}

function closeModal() {
    hideAllErrors();
}
// Login form validation
document.addEventListener('DOMContentLoaded', () => {
    console.log('Login validation loaded');

    const loginForm = document.querySelector('form[action="/login"]');

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const email = document.getElementById(' login-email').value;
            const password = document.getElementById('login-password').value;

            if (validateLoginForm(email, password)) {
                loginForm.submit();
            }
        });
    }
});

function validateLoginForm(email, password) {
    let isValid = true;

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('Please enter a valid email address');
        isValid = false;
    }

    // Password validation
    if (password.length < 8) {
        showError('Password must be at least 8 characters');
        isValid = false;
    }

    return isValid;
}

