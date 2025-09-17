<?php
$pageTitle = 'Inscription';
$extraScripts = '<script src="/assets/js/auth.js"></script>';
?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Inscription</h1>
            <p>Créez votre compte créateur</p>
        </div>

        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <ul class="error-list">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= $error; ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST" class="auth-form">
            <div class="form-group">
                <label for="name">Nom</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required 
                           minlength="2"
                           maxlength="50"
                           autocomplete="name"
                           value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>"
                           placeholder="Votre nom">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           autocomplete="email"
                           value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
                           placeholder="votre@email.com">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
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
                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        J'accepte les <a href="/terms" target="_blank">conditions d'utilisation</a>
                        et la <a href="/privacy" target="_blank">politique de confidentialité</a>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Créer mon compte
                </button>
            </div>

            <div class="auth-links">
                <span>Déjà un compte ?</span>
                <a href="/login">Se connecter</a>
            </div>
        </form>
    </div>
</div>
