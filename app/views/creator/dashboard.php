<?php
$stats = $stats ?? [];
$recentDonations = $stats['recent_donations'] ?? [];
$totalDonations = $stats['total_donations'] ?? 0;
$donationGoal = $stats['donation_goal'] ?? 0;
$progress = isset($stats['progress_percentage']) ? (int) $stats['progress_percentage'] : 0;
$goalReached = !empty($stats['goal_reached']);
$closeToGoal = !empty($stats['close_to_goal']);
$remainingAmount = $stats['remaining_amount'] ?? max(0, $donationGoal - $totalDonations);

$formatMoney = static function ($value) {
    if (function_exists('format_money')) {
        return format_money($value);
    }

    return number_format((float) $value, 2, ',', ' ') . ' €';
};
?>

<section class="dashboard-progress card">
    <div class="dashboard-progress-header">
        <div class="dashboard-progress-title">
            <span class="dashboard-progress-icon">🎯</span>
            <span>Objectif de dons</span>
        </div>
    </div>
    <div class="dashboard-progress-bar" style="--progress-value: <?= $progress ?>">
        <div class="dashboard-progress-value"></div>
        <div class="dashboard-progress-label"><?= $progress ?>%</div>
    </div>
    <div class="dashboard-progress-summary">
        <strong><?= $formatMoney($totalDonations) ?></strong>
        <span class="dashboard-progress-separator">/</span>
        <strong><?= $formatMoney($donationGoal) ?></strong>
        <?php if ($goalReached): ?>
            <span class="dashboard-progress-badge success">🎉 Objectif atteint !</span>
        <?php elseif ($closeToGoal): ?>
            <span class="dashboard-progress-badge warning">Plus que <?= $formatMoney($remainingAmount) ?> !</span>
        <?php endif; ?>
    </div>
    <div class="dashboard-progress-note">
        <?php if ($goalReached): ?>
            Félicitations, tu as atteint ton objectif !
        <?php elseif ($closeToGoal): ?>
            Tu es tout près du but, courage !
        <?php else: ?>
            Continue sur ta lancée, tu y es presque !
        <?php endif; ?>
    </div>
</section>

<section class="card dashboard-card">
    <div class="dashboard-card-header">
        <h2>Derniers dons</h2>
        <button class="btn btn-accent" type="button" onclick="openDonationModal()">
            <i class="fas fa-heart"></i>
            <span>Ajouter un don</span>
        </button>
    </div>
    <div class="dashboard-table-wrapper">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Donateur</th>
                    <th>Email</th>
                    <th class="text-right">Montant</th>
                    <th class="text-center">Date</th>
                    <th>Type</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($recentDonations)): ?>
                <?php foreach ($recentDonations as $donation): ?>
                    <?php
                    $profileUrl = $donation['profile_url'] ?? '#';
                    $donorName = $donation['donor_name'] ?? 'Anonyme';
                    $donorEmail = $donation['donor_email'] ?? '—';
                    $amount = $donation['amount'] ?? 0;
                    $comment = trim($donation['comment'] ?? '');
                    $initials = $donation['initials'] ?? '—';
                    $donationType = $donation['donation_type'] ?? '';
                    $typeColor = $donation['type_color'] ?? '#f59e42';
                    $createdAt = $donation['created_at'] ?? null;

                    if ($createdAt instanceof DateTimeInterface) {
                        $dateLabel = $createdAt->format('d/m/Y');
                        $timeLabel = $createdAt->format('H:i');
                    } else {
                        $timestamp = is_string($createdAt) ? strtotime($createdAt) : time();
                        $dateLabel = date('d/m/Y', $timestamp);
                        $timeLabel = date('H:i', $timestamp);
                    }
                    ?>
                    <tr class="dashboard-table-row" onclick="window.location.href='<?= htmlspecialchars($profileUrl) ?>'">
                        <td>
                            <div class="donor-cell">
                                <div class="donor-avatar"><?= htmlspecialchars($initials) ?></div>
                                <a href="<?= htmlspecialchars($profileUrl) ?>" class="donor-link" title="Voir le profil de <?= htmlspecialchars($donorName) ?>" onclick="event.stopPropagation();">
                                    <span class="donor-name" title="<?= htmlspecialchars($donorName) ?>"><?= htmlspecialchars($donorName) ?></span>
                                </a>
                            </div>
                        </td>
                        <td>
                            <span class="donor-email" title="<?= htmlspecialchars($donorEmail) ?>"><?= htmlspecialchars($donorEmail) ?></span>
                        </td>
                        <td class="text-right">
                            <span class="donation-amount"><?= $formatMoney($amount) ?></span>
                        </td>
                        <td class="text-center">
                            <div class="donation-date">
                                <span><?= htmlspecialchars($dateLabel) ?></span>
                                <span class="donation-time"><?= htmlspecialchars($timeLabel) ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="donation-type-badge" style="color: <?= htmlspecialchars($typeColor) ?>; background-color: <?= htmlspecialchars($typeColor) ?>22; border-color: <?= htmlspecialchars($typeColor) ?>33;">
                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $donationType))) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($comment !== ''): ?>
                                <span class="donation-comment" title="<?= htmlspecialchars($comment) ?>"><?= htmlspecialchars($comment) ?></span>
                            <?php else: ?>
                                <span class="donation-comment empty">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="dashboard-table-empty">Aucun don récent</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="dashboard-card-footer">
        <a href="/dashboard/donators" class="btn btn-outline-primary">Voir tous les donateurs</a>
    </div>
</section>

<section class="card useful-links-card">
    <h2>Liens Utiles</h2>
    <div class="useful-links-grid">
        <a class="useful-link-card" href="https://www.amazon.fr/hz/wishlist/intro" target="_blank" rel="noopener">
            <i class="fab fa-amazon"></i>
            <span>Amazon</span>
        </a>
        <a class="useful-link-card" href="https://throne.com/nicofriend" target="_blank" rel="noopener">
            <i class="fa-solid fa-crown"></i>
            <span>Throne</span>
        </a>
        <a class="useful-link-card" href="https://mail.google.com/mail/u/0/#inbox" target="_blank" rel="noopener">
            <i class="fa-solid fa-envelope"></i>
            <span>Gmail</span>
        </a>
        <a class="useful-link-card" href="https://creator.twitch.tv/" target="_blank" rel="noopener">
            <i class="fab fa-twitch"></i>
            <span>Twitch Creator</span>
        </a>
    </div>
</section>
