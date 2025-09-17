<?php
$this->setTitle('Connexion');
$this->addScript('/assets/js/auth.js');
?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Connexion</h1>
            <p>Connectez-vous à votre compte créateur</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

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
                           autocomplete="current-password"
                           placeholder="Votre mot de passe">
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </div>

            <div class="auth-links">
                <a href="/forgot-password">Mot de passe oublié ?</a>
                <span class="separator">•</span>
                <a href="/register">Créer un compte</a>
            </div>
        </form>
    </div>
</div>
