<?php
$this->setTitle('Connexion');
$this->addScript('/assets/js/auth.js');
?>

<div class="auth-header">
    <h1>Connexion</h1>
    <p>Connectez-vous à votre compte créatrice</p>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="flash-stack">
        <div class="flash-message flash-message--error">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="flash-stack">
        <div class="flash-message flash-message--success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<form action="/login" method="POST" class="auth-form">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

    <div class="form-group">
        <label for="email">Email</label>
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input
                type="email"
                id="email"
                name="email"
                required
                autocomplete="email"
                value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
                placeholder="votre@email.com"
            >
        </div>
    </div>

    <div class="form-group">
        <label for="password">Mot de passe</label>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Votre mot de passe"
            >
            <button type="button" class="toggle-password">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="form-group checkbox-group">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Se souvenir de moi</label>
    </div>

    <button type="submit" class="btn">
        <i class="fas fa-sign-in-alt"></i>
        Se connecter
    </button>

    <div class="auth-footer">
        <a href="/forgot-password">Mot de passe oublié ?</a>
        <span class="separator">•</span>
        <a href="/register">Créer un compte</a>
    </div>
</form>
