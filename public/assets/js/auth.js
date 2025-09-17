document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'affichage du mot de passe
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }

    // Validation du formulaire
    const authForm = document.querySelector('.auth-form');
    if (authForm) {
        authForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            let isValid = true;

            // Validation de l'email
            if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                showError(email, 'Veuillez entrer une adresse email valide');
                isValid = false;
            } else {
                removeError(email);
            }

            // Validation du mot de passe
            if (password.value.length < 6) {
                showError(password, 'Le mot de passe doit contenir au moins 6 caractères');
                isValid = false;
            } else {
                removeError(password);
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});

function showError(input, message) {
    const formGroup = input.closest('.form-group');
    let errorDiv = formGroup.querySelector('.error-message');
    
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        formGroup.appendChild(errorDiv);
    }
    
    errorDiv.textContent = message;
    formGroup.classList.add('has-error');
}

function removeError(input) {
    const formGroup = input.closest('.form-group');
    const errorDiv = formGroup.querySelector('.error-message');
    
    if (errorDiv) {
        errorDiv.remove();
    }
    
    formGroup.classList.remove('has-error');
}
