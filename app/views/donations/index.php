<?php
// Vue : Donations - Index
?>
<div class="container">
    <h1>Dons reçus</h1>
    <?php if (!empty($donations)) : ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Donateur</th>
                    <th>Montant</th>
                    <th>Type</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donations as $donation) : ?>
                    <tr>
                        <td><?= htmlspecialchars($donation['created_at'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($donation['donor_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($donation['amount'] ?? '0') ?> €</td>
                        <td><?= htmlspecialchars($donation['donation_type'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($donation['comment'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="alert alert-info">Aucun don enregistré pour le moment.</div>
    <?php endif; ?>
    <hr>
    <h3>Objectif : <?= htmlspecialchars($donation_goal ?? 0) ?> €</h3>
    <div>Montant total reçu : <strong><?= htmlspecialchars($total_donations ?? 0) ?> €</strong></div>
    <div>Progression : <strong><?= htmlspecialchars($progress_percentage ?? 0) ?> %</strong></div>
</div>
