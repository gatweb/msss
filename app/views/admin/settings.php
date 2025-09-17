<?php $pageTitle = 'Paramètres système'; ?>

<div class="settings-manager">
    <div class="page-header">
        <h1>Paramètres Système</h1>
        <div class="header-actions">
            <button class="btn btn-primary" onclick="saveAllSettings()">
                <i class="fas fa-save"></i>
                Enregistrer
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo e($_SESSION['success']); ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo e($_SESSION['error']); ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="settings-grid">
        <!-- Configuration générale -->
        <div class="settings-section">
            <h2>Configuration Générale</h2>
            <div class="settings-form">
                <div class="form-group">
                    <label for="siteName">Nom du site</label>
                    <input type="text" id="siteName" name="site_name" value="<?= e($settings['site_name']) ?>">
                </div>
                <div class="form-group">
                    <label for="siteDescription">Description</label>
                    <textarea id="siteDescription" name="site_description" rows="3"><?= e($settings['site_description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="contactEmail">Email de contact</label>
                    <input type="email" id="contactEmail" name="contact_email" value="<?= e($settings['contact_email']) ?>">
                </div>
                <div class="form-group">
                    <label for="maintenanceMode">
                        <input type="checkbox" id="maintenanceMode" name="maintenance_mode" <?= $settings['maintenance_mode'] ? 'checked' : '' ?>>
                        Mode maintenance
                    </label>
                </div>
            </div>
        </div>

        <!-- Paramètres des dons -->
        <div class="settings-section">
            <h2>Paramètres des Dons</h2>
            <div class="settings-form">
                <div class="form-group">
                    <label for="minDonation">Don minimum (€)</label>
                    <input type="number" id="minDonation" name="min_donation" value="<?= e($settings['min_donation']) ?>" min="1" step="0.01">
                </div>
                <div class="form-group">
                    <label for="maxDonation">Don maximum (€)</label>
                    <input type="number" id="maxDonation" name="max_donation" value="<?= e($settings['max_donation']) ?>" min="1" step="0.01">
                </div>
                <div class="form-group">
                    <label for="platformFee">
                        Commission plateforme (%)
                        <span class="help-text">Pourcentage prélevé sur chaque don</span>
                    </label>
                    <input type="number" id="platformFee" name="platform_fee" value="<?= e($settings['platform_fee']) ?>" min="0" max="100" step="0.1">
                </div>
                <div class="form-group">
                    <label for="paymentMethods">Méthodes de paiement</label>
                    <div class="checkbox-group">
                        <?php foreach ($paymentMethods as $method): ?>
                            <label>
                                <input type="checkbox" name="payment_methods[]" value="<?= $method['id'] ?>" 
                                       <?= in_array($method['id'], $settings['enabled_payment_methods']) ? 'checked' : '' ?>>
                                <?= e($method['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paramètres des créatrices -->
        <div class="settings-section">
            <h2>Paramètres des Créatrices</h2>
            <div class="settings-form">
                <div class="form-group">
                    <label for="maxPacks">Nombre maximum de packs</label>
                    <input type="number" id="maxPacks" name="max_packs" value="<?= e($settings['max_packs']) ?>" min="1">
                </div>
                <div class="form-group">
                    <label for="maxFileSize">Taille maximum des fichiers (MB)</label>
                    <input type="number" id="maxFileSize" name="max_file_size" value="<?= e($settings['max_file_size']) ?>" min="1">
                </div>
                <div class="form-group">
                    <label for="allowedFileTypes">Types de fichiers autorisés</label>
                    <input type="text" id="allowedFileTypes" name="allowed_file_types" value="<?= e($settings['allowed_file_types']) ?>" placeholder="jpg,png,gif">
                    <span class="help-text">Séparer les extensions par des virgules</span>
                </div>
                <div class="form-group">
                    <label for="autoApprove">
                        <input type="checkbox" id="autoApprove" name="auto_approve" <?= $settings['auto_approve'] ? 'checked' : '' ?>>
                        Approbation automatique des créatrices
                    </label>
                </div>
            </div>
        </div>

        <!-- Paramètres de sécurité -->
        <div class="settings-section">
            <h2>Sécurité</h2>
            <div class="settings-form">
                <div class="form-group">
                    <label for="maxLoginAttempts">Tentatives de connexion max</label>
                    <input type="number" id="maxLoginAttempts" name="max_login_attempts" value="<?= e($settings['max_login_attempts']) ?>" min="1">
                </div>
                <div class="form-group">
                    <label for="lockoutDuration">Durée de blocage (minutes)</label>
                    <input type="number" id="lockoutDuration" name="lockout_duration" value="<?= e($settings['lockout_duration']) ?>" min="1">
                </div>
                <div class="form-group">
                    <label for="sessionTimeout">Expiration de session (minutes)</label>
                    <input type="number" id="sessionTimeout" name="session_timeout" value="<?= e($settings['session_timeout']) ?>" min="1">
                </div>
                <div class="form-group">
                    <label for="requireStrongPasswords">
                        <input type="checkbox" id="requireStrongPasswords" name="require_strong_passwords" <?= $settings['require_strong_passwords'] ? 'checked' : '' ?>>
                        Exiger des mots de passe forts
                    </label>
                </div>
                <div class="form-group">
                    <label for="enable2FA">
                        <input type="checkbox" id="enable2FA" name="enable_2fa" <?= $settings['enable_2fa'] ? 'checked' : '' ?>>
                        Activer l'authentification 2FA
                    </label>
                </div>
            </div>
        </div>

        <!-- Paramètres de notification -->
        <div class="settings-section">
            <h2>Notifications</h2>
            <div class="settings-form">
                <div class="form-group">
                    <label for="emailNotifications">Notifications par email</label>
                    <div class="checkbox-group">
                        <?php foreach ($emailNotificationTypes as $type): ?>
                            <label>
                                <input type="checkbox" name="email_notifications[]" value="<?= $type['id'] ?>"
                                       <?= in_array($type['id'], $settings['enabled_email_notifications']) ? 'checked' : '' ?>>
                                <?= e($type['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="smtpHost">Serveur SMTP</label>
                    <input type="text" id="smtpHost" name="smtp_host" value="<?= e($settings['smtp_host']) ?>">
                </div>
                <div class="form-group">
                    <label for="smtpPort">Port SMTP</label>
                    <input type="number" id="smtpPort" name="smtp_port" value="<?= e($settings['smtp_port']) ?>">
                </div>
                <div class="form-group">
                    <label for="smtpUser">Utilisateur SMTP</label>
                    <input type="text" id="smtpUser" name="smtp_user" value="<?= e($settings['smtp_user']) ?>">
                </div>
                <div class="form-group">
                    <label for="smtpPass">Mot de passe SMTP</label>
                    <input type="password" id="smtpPass" name="smtp_pass" value="<?= e($settings['smtp_pass']) ?>">
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-manager {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
    margin-top: 2rem;
}

.settings-section {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.settings-section h2 {
    margin: 0 0 1.5rem;
    font-size: 1.2rem;
    color: var(--text-color);
}

.settings-form {
    display: grid;
    gap: 1.5rem;
}

.form-group {
    display: grid;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: var(--text-color);
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="number"],
.form-group textarea {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    font-size: 0.95rem;
    width: 100%;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.checkbox-group {
    display: grid;
    gap: 0.75rem;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: normal;
}

.help-text {
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.25rem;
}

/* Responsive */
@media (max-width: 1200px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .settings-manager {
        padding: 1rem;
    }
}
</style>

<script>
function saveAllSettings() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/settings/save';
    
    // Ajouter tous les champs
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        if (input.type === 'checkbox') {
            if (input.checked) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = input.name;
                hiddenInput.value = '1';
                form.appendChild(hiddenInput);
            }
        } else {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = input.name;
            hiddenInput.value = input.value;
            form.appendChild(hiddenInput);
        }
    });
    
    // Ajouter le token CSRF
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = '<?= generateCsrfToken() ?>';
    form.appendChild(csrfInput);
    
    // Soumettre le formulaire
    document.body.appendChild(form);
    form.submit();
}

// Validation en temps réel
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', function() {
        const min = parseFloat(this.min);
        const max = parseFloat(this.max);
        const value = parseFloat(this.value);
        
        if (min !== undefined && value < min) {
            this.value = min;
        }
        if (max !== undefined && value > max) {
            this.value = max;
        }
    });
});

// Test de la configuration SMTP
document.querySelector('#smtpHost').addEventListener('change', function() {
    const host = this.value;
    const port = document.querySelector('#smtpPort').value;
    
    if (host && port) {
        testSmtpConnection(host, port);
    }
});

function testSmtpConnection(host, port) {
    fetch('/admin/settings/test-smtp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= generateCsrfToken() ?>'
        },
        body: JSON.stringify({ host, port })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Connexion SMTP réussie', 'success');
        } else {
            showNotification('Erreur de connexion SMTP: ' + data.error, 'error');
        }
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    
    document.querySelector('.settings-manager').insertBefore(notification, document.querySelector('.settings-grid'));
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>

