document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const firstName = document.getElementById('first_name');
    const lastName = document.getElementById('last_name');

    // Error message display function
    function showError(input, message) {
        const formGroup = input.parentElement;
        const errorDiv = formGroup.querySelector('.error-message') || document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        if (!formGroup.querySelector('.error-message')) {
            formGroup.appendChild(errorDiv);
        }
        input.classList.add('error-input');
    }

    // Success message display function
    function showSuccess(input) {
        const formGroup = input.parentElement;
        const errorDiv = formGroup.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.remove();
        }
        input.classList.remove('error-input');
    }

    // Email validation
    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    // Real-time validation
    email.addEventListener('input', function() {
        if (!validateEmail(email.value)) {
            showError(email, 'Please enter a valid email address');
        } else {
            showSuccess(email);
        }
    });

    password.addEventListener('input', function() {
        if (password.value.length < 8) {
            showError(password, 'Password must be at least 8 characters long');
        } else {
            showSuccess(password);
        }
    });

    confirmPassword.addEventListener('input', function() {
        if (confirmPassword.value !== password.value) {
            showError(confirmPassword, 'Passwords do not match');
        } else {
            showSuccess(confirmPassword);
        }
    });

    firstName.addEventListener('input', function() {
        if (firstName.value.trim() === '') {
            showError(firstName, 'First name is required');
        } else {
            showSuccess(firstName);
        }
    });

    lastName.addEventListener('input', function() {
        if (lastName.value.trim() === '') {
            showError(lastName, 'Last name is required');
        } else {
            showSuccess(lastName);
        }
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Validate email
        if (!validateEmail(email.value)) {
            showError(email, 'Please enter a valid email address');
            isValid = false;
        }

        // Validate password
        if (password.value.length < 8) {
            showError(password, 'Password must be at least 8 characters long');
            isValid = false;
        }

        // Validate password confirmation
        if (confirmPassword.value !== password.value) {
            showError(confirmPassword, 'Passwords do not match');
            isValid = false;
        }

        // Validate first name
        if (firstName.value.trim() === '') {
            showError(firstName, 'First name is required');
            isValid = false;
        }

        // Validate last name
        if (lastName.value.trim() === '') {
            showError(lastName, 'Last name is required');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
}); 