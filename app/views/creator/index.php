<!-- Bloc stats/objectifs avec barre de progression -->
<?php
// Calculs stats pour toutes les cartes
$objectif = isset($stats['donation_goal']) && $stats['donation_goal'] !== null ? $stats['donation_goal'] : 500;
$total = isset($stats['total_donations']) && $stats['total_donations'] !== null ? $stats['total_donations'] : 0;
$pourcent = $objectif > 0 ? min(100, round(($total/$objectif)*100)) : 0;
?>






<div style="max-width:900px;margin:25px auto 1.5rem auto;background:#fff;border-radius:12px;padding:2em 2.5em;box-shadow:0 2px 19px #cfb8d8;">
  <div style="font-size:2em;color:#e11d48;font-weight:bold;display:flex;align-items:center;gap:0.5em;margin-bottom:0.3em;">
    <span style="font-size:1.2em;">🎯</span> Objectif de dons
  </div>
  <div style="width:90%;margin:0.8em 0 0.4em 0;">
    <div style="background:#e5e7eb;border-radius:1em;height:24px;position:relative;overflow:hidden;">
      <div style="background:linear-gradient(90deg,#34d399,#fbbf24);height:100%;width:<?= $pourcent ?>%;transition:width 1s;position:absolute;left:0;top:0;"></div>
      <div style="position:absolute;left:calc(<?= $pourcent ?>% - 20px);top:1px;">
        <span style="font-size:1em;font-weight:700;color:#059669;">
          (<?= $pourcent ?>%)
        </span>
      </div>
    </div>
  </div>
  <div style="font-size:1.15em;margin-top:0.4em; color:#555;">
    <b style="font-weight:800; font-size:1.1em; color:#111;"><?= number_format($total,2,',',' ') ?> €</b> / <b style="font-weight:800; font-size:1.1em; color:#111;"><?= number_format($objectif ?? 0,2,',',' ') ?> €</b>
    <?php if ($pourcent >= 100): ?>
      <span style="color:#059669;font-weight:bold;margin-left:0.5em; font-size:1.05em;">🎉 Objectif atteint !</span>
    <?php elseif ($pourcent >= 80): ?>
      <span style="color:#34d399;font-weight:bold;margin-left:0.5em; font-size:1.05em;">Plus que <?= number_format($objectif-$total,2,',',' ') ?> € !</span>
    <?php endif; ?>
  </div>

  <div style="margin-top:0.4em;font-size:0.97em;color:#059669;">
    <?php if ($pourcent >= 100): ?>
      Félicitations, tu as atteint ton objectif !
    <?php elseif ($pourcent >= 80): ?>
      Tu es tout près du but, courage !
    <?php else: ?>

    <?php endif; ?>

</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-top:2.5rem; margin-bottom: 1rem;">
    <h3 style="margin:0; font-weight:700; color:#2563eb;">Derniers dons</h3>
    <button class="btn" style="background:#138a5e; color:#fff; font-weight:700; border-radius: 24px; padding: 0.65em 1.7em; box-shadow:0 2px 8px #138a5e33;" onclick="openDonationModal()">
        <i class="fas fa-heart left" style="margin-right:0.5em;"></i>Ajouter un don
    </button>
</div>

 <div class="card" style="padding:1.1em 1.2em;margin-bottom:1.2rem;">
    <div class="donators-table enhanced-donators-table">
        <table class="table" style="margin-bottom:0;">
<style>
.table th, .table td {
    padding: 0.7em 0.8em !important;
    vertical-align: middle;
}
</style>
            <thead>
                <tr>
                    <th style="min-width:120px;">Donateur</th>
                    <th style="min-width:170px;">Email</th>
                    <th style="min-width:90px; text-align:right;">Montant</th>
                    <th style="min-width:110px; text-align:center;">Date</th>
                    <th style="min-width:90px;">Type</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($stats['recent_donations'])): ?>
                    <?php $rowIdx = 0; foreach ($stats['recent_donations'] as $donation): ?>
                        <tr style="transition:background 0.18s;cursor:pointer;<?php if ($rowIdx % 2 === 1) echo 'background:#f8f9fc;'; ?>" 
                            onmouseover="this.style.background='#f4f6fb'" 
                            onmouseout="this.style.background='<?php echo ($rowIdx % 2 === 1) ? '#f8f9fc' : ''; ?>'" 
                            onclick="window.location.href='/dashboard/donators/profile?email=<?= urlencode($donation['donor_email']) ?>'">
                            <td style="display:flex;align-items:center;gap:0.7em;">
                                <div style="width:38px;height:38px;border-radius:50%;background:#e0e7ff;color:#3b3b8c;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1em;box-shadow:0 1px 4px #e0e7ff;">
                                    <?php
                                    $initials = '';
                                    if (!empty($donation['donor_name'])) {
                                        $parts = explode(' ', $donation['donor_name']);
                                        foreach ($parts as $p) {
                                            if ($p && strlen($initials) < 2) $initials .= mb_strtoupper(mb_substr($p,0,1));
                                        }
                                    }
                                    echo $initials ?: '👤';
                                    ?>
                                </div>
                                <a href="/dashboard/donators/profile?email=<?= urlencode($donation['donor_email']) ?>"
                                    class="donator-link"
                                    title="Voir le profil de <?= htmlspecialchars($donation['donor_name'] ?? '') ?>"
                                    onclick="event.stopPropagation();">
                                    <span style="font-weight:600;max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-block;" title="<?= htmlspecialchars($donation['donor_name'] ?? '') ?>">
                                        <?= htmlspecialchars($donation['donor_name'] ?? 'Anonyme') ?>
                                    </span>
                                </a>
                            </td>
                            <td style="color:#7A7A8C;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($donation['donor_email']) ?>">
                                <?= htmlspecialchars($donation['donor_email']) ?>
                            </td>
                            <td style="font-weight:700;color:#27ae60;">
                                <?= number_format($donation['amount'], 2, ',', ' ') ?> €
                            </td>
                            <td style="color:#2563eb;">
                                <?= date('d/m/Y H:i', strtotime($donation['created_at'])) ?>
                            </td>
                            <td>
                                <?php
                                $type = strtolower($donation['donation_type']);
                                $typeColors = [
                                    'fan_fidele' => '#ffd700',
                                    'pack' => '#7C83FD',
                                    'ponctuel' => '#34d399',
                                    'default' => '#f59e42',
                                ];
                                $color = $typeColors[$type] ?? $typeColors['default'];
                                ?>
                                <span class="badge" style="background:<?= $color ?>22;color:<?= $color ?>;border:1px solid <?= $color ?>33;">
                                    <?= htmlspecialchars(ucfirst($donation['donation_type'])) ?>
                                </span>
                            </td>
                            <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <?= !empty($donation['comment']) ? htmlspecialchars($donation['comment']) : '<span style=\'color:#bbb\'>-</span>' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center;color:#888;">Aucun don récent</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="text-align:right;margin-top:1em;">
        <a href="/dashboard/donators" class="btn btn-outline-primary">Voir tous les donateurs</a>
    </div>
</div>

<!-- Liens utiles -->
    <div class="row useful-links">
        <div class="col s12">
            <div class="card-panel" style="background:#f0f9ff;border-radius:1.5em;box-shadow:0 2px 12px #c7d2fe;">
                <h3 style="font-weight:700;color:#2563eb;margin-bottom:1.5rem;">Liens Utiles</h3>
                <div class="useful-links-grid">
                    <a class="useful-link-card" href="https://www.amazon.fr/hz/wishlist/intro" target="_blank">
                        <i class="fab fa-amazon"></i>
                        <span>Amazon</span>
                    </a>
                    <a class="useful-link-card" href="https://throne.com/nicofriend" target="_blank">
                        <i class="fa-solid fa-crown"></i>
                        <span>Throne</span>
                    </a>
                    <a class="useful-link-card" href="https://mail.google.com/mail/u/0/#inbox" target="_blank">
                        <i class="fa-solid fa-envelope"></i>
                        <span>Gmail</span>
                    </a>
                    <a class="useful-link-card" href="https://drive.google.com/drive/my-drive" target="_blank">
                        <i class="fab fa-google-drive"></i>
                        <span>GDrive</span>
                    </a>
                    <a class="useful-link-card" href="https://paypal.me/msstephanie59" target="_blank">
                        <i class="fab fa-paypal"></i>
                        <span>PayPal</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

<style>
.useful-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 1.2rem;
    margin-bottom: 1rem;
    margin-top: 1rem;
}
.useful-link-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 1em;
    box-shadow: 0 2px 8px #c7d2fe55;
    padding: 1.2em 1em 1em 1em;
    text-decoration: none;
    color: #2563eb;
    font-weight: 600;
    font-size: 1.1em;
    transition: box-shadow 0.2s, background 0.2s, color 0.2s;
    min-height: 90px;
}
.useful-link-card i {
    font-size: 2.2em;
    margin-bottom: 0.5em;
    color: #6366f1;
}
.useful-link-card:hover {
    background: #e0e7ff;
    color: #1e40af;
    box-shadow: 0 4px 16px #a5b4fc99;
}
.useful-link-card span {
    margin-top: 0.2em;
    font-size: 1.08em;
}
@media (max-width: 600px) {
    .useful-links-grid {
        grid-template-columns: 1fr 1fr;
    }
    .useful-link-card {
        font-size: 1em;
        min-height: 70px;
    }
}

.donator-link {
    color: #2563eb; /* Couleur du lien */
    text-decoration: none;
    font-weight: 500;
}
.donator-link:hover {
    text-decoration: underline;
    color: #1d4ed8; /* Couleur au survol */
}
</style>

<!-- Fin de la section Liens utiles -->

    <!-- Modal d'ajout de don -->
    <div id="donationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ajouter un don</h2>
                <button class="close-modal" onclick="closeDonationModal()">&times;</button>
            </div>
            <div id="donationModalMessage" style="display:none;padding:1em;margin-bottom:1em;border-radius:6px;font-weight:600;"></div>
            <form class="donation-form" method="POST" action="/dashboard/donations/add" onsubmit="return handleDonationFormSubmit(event)">
                <div class="form-group">
                    <label for="donorName">Nom du donateur *</label>
                    <input type="text" id="donorName" name="donor_name" required>
                </div>

                <div class="form-group">
                    <label for="donorEmail">Email du donateur</label>
                    <input type="email" id="donorEmail" name="donor_email">
                    <small>Optionnel - pour le suivi et les remerciements</small>
                </div>

                <div class="form-group">
                    <label for="amount">Montant *</label>
                    <div class="amount-input">
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required>
                        <span class="currency">€</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="donationType">Type de don *</label>
                    <select id="donationType" name="donation_type" required>
                        <option value="one_time">Ponctuel</option>
                        <option value="monthly">Mensuel</option>
                        <option value="pack">Pack</option>
                    </select>
                </div>

                <div class="form-group" id="packSelection" style="display: none;">
                    <label for="packId">Pack choisi</label>
                    <select id="packId" name="pack_id">
                        <?php if (!empty($packs) && is_array($packs)): foreach ($packs as $pack): ?>
                            <option value="<?= $pack['id'] ?>"><?= htmlspecialchars($pack['name']) ?> - <?= number_format($pack['price'], 2) ?>€</option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comment">Commentaire</label>
                    <textarea id="comment" name="comment" rows="3"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeDonationModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer le don</button>
                </div>
            </form>
        </div>
    </div>

<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.modal-content {
    position: relative;
    background-color: white;
    margin: 5% auto;
    padding: 0;
    width: 90%;
    max-width: 600px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
}

.modal-header h2 {
    margin: 0;
    color: #2c3e50;
}

.close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
}

.donation-form {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2c3e50;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.form-group small {
    display: block;
    margin-top: 0.25rem;
    color: #666;
    font-size: 0.875rem;
}

.amount-input {
    position: relative;
}

.amount-input input {
    padding-right: 2rem;
}

.amount-input .currency {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
}
</style>

<script>
function openDonationModal() {
    document.getElementById('donationModal').style.display = 'block';
}

function closeDonationModal() {
    document.getElementById('donationModal').style.display = 'none';
}

document.getElementById('donationType').addEventListener('change', function() {
    const packSelection = document.getElementById('packSelection');
    if (this.value === 'pack') {
        packSelection.style.display = 'block';
    } else {
        packSelection.style.display = 'none';
    }
});

// Fermer la modal si on clique en dehors
window.onclick = function(event) {
    const modal = document.getElementById('donationModal');
    if (event.target === modal) {
        closeDonationModal();
    }
}

function sendReminder(donorName) {
    // TODO: Implémenter la fonction de relance
    alert(`Relance envoyée à ${donorName}`);
}

// Gestion du formulaire d'ajout de don (simulation front)
function handleDonationFormSubmit(event) {
    event.preventDefault();
    const messageDiv = document.getElementById('donationModalMessage');
    messageDiv.style.display = 'block';
    messageDiv.style.background = '#d1fae5';
    messageDiv.style.color = '#065f46';
    messageDiv.textContent = 'Don enregistré avec succès !';
    setTimeout(() => {
        closeDonationModal();
        messageDiv.style.display = 'none';
        messageDiv.textContent = '';
    }, 1500);
    // Ici tu pourrais faire un vrai POST AJAX si besoin
    return false;
}
</script>

<style>
.action-link i {
    font-size: 1.1em;
    transition: color 0.2s;
}
.action-link:hover i {
    color: #7C83FD;
}
</style>

<?php require_once APP_PATH . '/views/layouts/dashboard_footer.php'; ?>
