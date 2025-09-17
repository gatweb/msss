<?php
$pageTitle = 'Gestion des Dons';
ob_start();
?>

<div class="container">
    <div class="main-content">
        <div class="donation-form-container">
            <div class="progress-section">
                <h2>Objectif de dons : <?= number_format($donation_goal, 2) ?>€</h2>
                <p>Total actuel : <?= number_format($total_donations, 2) ?>€</p>
                <div class="progress-container">
                    <div id="progress-bar" class="progress-bar" style="width: <?= $progress_percentage ?>%"></div>
                </div>
            </div>

            <form method="POST" action="/donations/add" class="donation-form">
                <div class="form-group">
                    <label for="donor_name">Nom du donateur :</label>
                    <input type="text" id="donor_name" name="donor_name" required>
                </div>

                <div class="form-group">
                    <label for="amount">Montant (€) :</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="donation_type">Type de don :</label>
                    <select id="donation_type" name="donation_type" required>
                        <?php foreach ($valid_donation_types as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>">
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" name="submit_donation" class="button">
                    <i class="fas fa-gift"></i> Ajouter le don
                </button>
            </form>

            <div class="donations-list">
                <h2>Historique des dons</h2>
                <?php foreach ($donations as $donation): ?>
                    <div class="donation-item">
                        <div class="donation-header">
                            <h3><?= htmlspecialchars($donation['donor_name']) ?></h3>
                            <span class="donation-amount"><?= number_format($donation['amount'], 2) ?>€</span>
                            <span class="donation-type"><?= htmlspecialchars($donation['donation_type']) ?></span>
                        </div>

                        <div class="donation-timer" 
                             data-id="<?= $donation['id'] ?>"
                             data-status="<?= htmlspecialchars($donation['timer_status']) ?>"
                             data-elapsed="<?= htmlspecialchars($donation['timer_elapsed_seconds']) ?>">
                            <span class="timer-display">00:00:00</span>
                            <?php if ($donation['timer_status'] === 'running'): ?>
                                <button class="button button-stop" data-action="stop">
                                    <i class="fas fa-stop"></i>
                                </button>
                            <?php else: ?>
                                <button class="button button-start" data-action="start">
                                    <i class="fas fa-play"></i>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="donation-actions">
                            <button class="button button-edit" data-id="<?= $donation['id'] ?>">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                            <button class="button button-delete" data-id="<?= $donation['id'] ?>">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>

                        <div class="donation-comment">
                            <textarea placeholder="Ajouter un commentaire"><?= htmlspecialchars($donation['comment'] ?? '') ?></textarea>
                            <button class="button button-save" data-id="<?= $donation['id'] ?>">
                                <i class="fas fa-save"></i> Sauvegarder
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$extraScripts = '<script src="/assets/js/donations.js"></script>';
require APP_PATH . '/views/layouts/main.php';
?>
