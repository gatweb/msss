<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Réinitialisation du mot de passe</h1>
            <p>Choisissez votre nouveau mot de passe</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="/reset-password" method="POST" class="auth-form">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">

            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           minlength="8"
                           autocomplete="new-password"
                           placeholder="Minimum 8 caractères">
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength">
                    <div class="strength-bar"></div>
                    <span class="strength-text"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" 
                           id="password_confirm" 
                           name="password_confirm" 
                           required
                           minlength="8"
                           autocomplete="new-password"
                           placeholder="Retapez votre mot de passe">
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> Mettre à jour le mot de passe
                </button>
            </div>

            <div class="auth-links">
                <a href="/login">Retour à la connexion</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'affichage des mots de passe
    document.querySelectorAll('.toggle-password').forEach(function(button) {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });

    // Vérification de la force du mot de passe
    const passwordInput = document.querySelector('#password');
    const strengthBar = document.querySelector('.strength-bar');
    const strengthText = document.querySelector('.strength-text');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        
        strengthBar.style.width = strength.score * 25 + '%';
        strengthBar.className = 'strength-bar ' + strength.level;
        strengthText.textContent = strength.message;
    });

    // Vérification de la correspondance des mots de passe
    const passwordConfirm = document.querySelector('#password_confirm');
    const form = document.querySelector('form');

    form.addEventListener('submit', function(e) {
        if (passwordInput.value !== passwordConfirm.value) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas');
        }
    });
});

function checkPasswordStrength(password) {
    let score = 0;
    let message = '';

    // Longueur minimale
    if (password.length >= 8) score++;
    
    // Contient des chiffres
    if (/\d/.test(password)) score++;
    
    // Contient des minuscules et majuscules
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    
    // Contient des caractères spéciaux
    if (/[^A-Za-z0-9]/.test(password)) score++;

    switch (score) {
        case 0:
        case 1:
            message = 'Faible';
            level = 'weak';
            break;
        case 2:
            message = 'Moyen';
            level = 'medium';
            break;
        case 3:
            message = 'Fort';
            level = 'strong';
            break;
        case 4:
            message = 'Très fort';
            level = 'very-strong';
            break;
    }

    return { score, message, level };
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
