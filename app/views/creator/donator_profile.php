<?php
// app/views/creator/donator_profile.php
/** @var array $donator Donator info (name, email, total, last donation, etc.) */
/** @var array $donations List of all donations from this donator */
/** @var array $notes Notes & checkboxes for this donator (optional for now) */
?>

<div class="donator-profile-container">
    <a href="/dashboard/donators" class="btn btn-light mb-3">&larr; Retour à la liste</a>
    <div class="section-title-bar">
        <div>
            <h2>
                <span class="donator-avatar">
                    <?php echo strtoupper(substr($donator['donor_name'], 0, 1)); ?>
                </span>
                <?php echo htmlspecialchars($donator['donor_name']); ?> <small>(<?php echo htmlspecialchars($donator['donor_email']); ?>)</small>
                <?php
                // Badge couleur selon statut
                $status = $notes['crm_status'] ?? 'prospect';
                $statusLabels = [
                    'client' => ['Client', 'badge-success'],
                    'indesirable' => ['Indésirable', 'badge-danger'],
                    'attente' => ['En attente', 'badge-warning'],
                    'prospect' => ['Prospect', 'badge-info'],
                    'ancien' => ['Ancien', 'badge-secondary'],
                ];
                if (isset($statusLabels[$status])) {
                    echo '<span class="badge '.$statusLabels[$status][1].' ml-2">'.$statusLabels[$status][0].'</span>';
                }
                ?>
            </h2>
            <div class="donator-meta">
                <span>Total des dons : <strong><?php echo number_format($donator['total_amount'], 2, ',', ' '); ?> €</strong></span>
                <span>Dernier don : <strong><?php echo ($donator['last_donation'] ? date('d/m/Y H:i', strtotime($donator['last_donation'])) : '-'); ?></strong></span>
            </div>
        </div>
    </div>

    <form method="post" action="#" class="donator-notes-form">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="crm_phone">Téléphone</label>
                <input type="text" class="form-control" id="crm_phone" name="crm_phone" value="<?php echo htmlspecialchars($notes['crm_phone'] ?? ''); ?>" placeholder="Numéro de téléphone">
            </div>
            <div class="form-group col-md-6">
                <label for="crm_status">Statut du contact</label>
                <select class="form-control form-control-sm" id="crm_status" name="crm_status">
                    <option value="prospect" <?php if (($notes['crm_status'] ?? '') === 'prospect') echo 'selected'; ?>>Prospect</option>
                    <option value="client" <?php if (($notes['crm_status'] ?? '') === 'client') echo 'selected'; ?>>Client</option>
                    <option value="ancien" <?php if (($notes['crm_status'] ?? '') === 'ancien') echo 'selected'; ?>>Ancien</option>
                    <option value="attente" <?php if (($notes['crm_status'] ?? '') === 'attente') echo 'selected'; ?>>En attente</option>
                    <option value="indesirable" <?php if (($notes['crm_status'] ?? '') === 'indesirable') echo 'selected'; ?>>Indésirable</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label>Préférences personnelles :</label><br>
                <label class="checkbox-inline mr-3">
                    <input type="checkbox" name="pref_gift" <?php if (!empty($notes['pref_gift'])) echo 'checked'; ?>> Aime recevoir des cadeaux
                </label>
                <label class="checkbox-inline mr-3">
                    <input type="checkbox" name="pref_anonymous" <?php if (!empty($notes['pref_anonymous'])) echo 'checked'; ?>> Préfère l'anonymat
                </label>
                <label class="checkbox-inline mr-3">
                    <input type="checkbox" name="pref_birthday" <?php if (!empty($notes['pref_birthday'])) echo 'checked'; ?>> Anniversaire à fêter
                </label>
                <!-- Ajoutez ici d'autres préférences si besoin -->
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="crm_source">Source</label>
                <input type="text" class="form-control" id="crm_source" name="crm_source" value="<?php echo htmlspecialchars($notes['crm_source'] ?? ''); ?>" placeholder="Ex: Réseaux sociaux, bouche-à-oreille...">
            </div>
            <div class="form-group col-md-6">
                <label for="crm_first_contact">Date de premier contact</label>
                <input type="date" class="form-control" id="crm_first_contact" name="crm_first_contact" value="<?php echo htmlspecialchars($notes['crm_first_contact'] ?? ''); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="crm_birthday">Anniversaire</label>
                <?php
// Affichage anniversaire au format ISO pour input type=date
$birthdayVal = '';
if (!empty($notes['crm_birthday'])) {
    $ts = strtotime($notes['crm_birthday']);
    if ($ts) {
        $birthdayVal = date('Y-m-d', $ts);
    } else {
        $birthdayVal = htmlspecialchars($notes['crm_birthday']);
    }
}
?>
<input type="date" class="form-control" id="crm_birthday" name="crm_birthday" value="<?php echo $birthdayVal; ?>">
            </div>
        </div>
        <div class="form-group mt-3">
            <label>Suivi&nbsp;:</label><br>
            <label class="checkbox-inline mr-3">
                <input type="checkbox" name="merci_envoye" <?php if (!empty($notes['merci_envoye'])) echo 'checked'; ?>> Merci envoyé
            </label>
            <label class="checkbox-inline mr-3">
                <input type="checkbox" name="cadeau_envoye" <?php if (!empty($notes['cadeau_envoye'])) echo 'checked'; ?>> Cadeau expédié
            </label>
            <label class="checkbox-inline mr-3">
                <input type="checkbox" name="vip" <?php if (!empty($notes['vip'])) echo 'checked'; ?>> VIP
            </label>
            <label class="checkbox-inline mr-3">
                <input type="checkbox" name="fan_fidele" id="fan_fidele" <?php if (!empty($notes['fan_fidele'])) echo 'checked'; ?>> <span style="color:#e67e22;">Fan fidèle ⭐</span>
            </label>
            <?php if (!empty($notes['fan_fidele'])): ?>
                <?php
                $fan_fidele = !empty($notes['fan_fidele']);
                $fanStart = (!empty($notes['fan_fidele_since']) ? strtotime($notes['fan_fidele_since']) : false);
                $now = time();
                $elapsed = ($fan_fidele && $fanStart) ? ($now - $fanStart) : false;
                function format_elapsed($secs) {
                    $y = floor($secs/31536000); $secs %= 31536000;
                    $m = floor($secs/2592000); $secs %= 2592000;
                    $d = floor($secs/86400); $secs %= 86400;
                    $h = floor($secs/3600); $secs %= 3600;
                    $min = floor($secs/60); $s = $secs%60;
                    $out = [];
                    if ($y) $out[] = $y.' an'.($y>1?'s':'');
                    if ($m) $out[] = $m.' mois';
                    if ($d) $out[] = $d.' j';
                    if ($h) $out[] = $h.'h';
                    if ($min) $out[] = $min.'min';
                    if ($s && !$y && !$m) $out[] = $s.'s';
                    return implode(' ', $out);
                }
                ?>
                <?php if ($fan_fidele): ?>
    <span class="ml-2 fan-counter">
        <i class="fa fa-clock-o"></i> Fan depuis : <strong><?php echo ($elapsed ? format_elapsed($elapsed) : 'depuis ?'); ?></strong>
    </span>
<?php endif; ?>
<?php endif; ?>
        </div>
        <div class="form-group">
            <label for="commentaire">Commentaire interne :</label>
            <textarea class="form-control" id="commentaire" name="commentaire" rows="3" placeholder="Ajoutez une note..."><?php echo htmlspecialchars($notes['commentaire'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer le suivi</button>
    </form>
    <hr>

    <div class="mt-4">
        <h4>Historique des dons</h4>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donations as $don): ?>
                <tr>
                    <td><?php echo ($don['created_at'] ? date('d/m/Y H:i', strtotime($don['created_at'])) : '-'); ?></td>
                    <td><?php echo number_format($don['amount'], 2, ',', ' '); ?> €</td>
                    <td><?php echo isset($don['message']) && $don['message'] !== null ? htmlspecialchars($don['message']) : ''; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        <a href="mailto:<?php echo htmlspecialchars($donator['donor_email']); ?>" class="btn btn-outline-primary">Envoyer un message</a>
    </div>
</div>

<style>
.donator-profile-container {
    max-width: 600px;
    margin: 1.2rem auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    padding: 1.2rem 1.2rem;
}
.section-title-bar {
    border-bottom: 1px solid #eee;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
}
.donator-avatar {
    background: #e9ecef;
    color: #5c636a;
    border-radius: 50%;
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.4rem;
    margin-right: 12px;
}
.donator-summary .badge {
    margin-right: 8px;
    font-size: 1rem;
}
.donator-notes-form {
    margin-top: 0.7rem;
    margin-bottom: 1rem;
}
.donator-notes-form .form-control, .donator-notes-form .form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.95rem;
    height: 2rem;
}
.donator-notes-form .form-group {
    margin-bottom: 0.5rem;
}
.donator-notes-form label {
    font-size: 0.98rem;
    margin-bottom: 0.15rem;
}
.donator-notes-form .checkbox-inline {
    margin-bottom: 0.2rem;
}
.donator-notes-form textarea.form-control {
    min-height: 2.2rem;
    font-size: 0.97rem;
}
.badge {
    display: inline-block;
    padding: 0.25em 0.7em;
    font-size: 0.93em;
    font-weight: 500;
    border-radius: 0.7em;
    vertical-align: middle;
}
.badge-success { background: #27ae60; color: #fff; }
.badge-danger { background: #e74c3c; color: #fff; }
.badge-warning { background: #f39c12; color: #fff; }
.badge-info { background: #3498db; color: #fff; }
.badge-secondary { background: #95a5a6; color: #fff; }
.fan-counter {
    color: #e67e22;
    font-weight: bold;
    font-size: 0.97em;
}
</style>
