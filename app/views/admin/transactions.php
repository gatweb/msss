
<div class="transactions-manager">
    <div class="page-header">
        <h1>Transactions & Dons</h1>
        <div class="header-actions">
            <form method="GET" class="filters-form">
                <input type="text" name="search" placeholder="Recherche créatrice ou donateur..." value="<?= e($_GET['search'] ?? '') ?>">
                <input type="date" name="start_date" value="<?= e($_GET['start_date'] ?? '') ?>">
                <input type="date" name="end_date" value="<?= e($_GET['end_date'] ?? '') ?>">
                <select name="creator_id">
                    <option value="">Toutes les créatrices</option>
                    <?php foreach ($creators as $creator): ?>
                        <option value="<?= $creator['id'] ?>" <?= (($_GET['creator_id'] ?? '') == $creator['id']) ? 'selected' : '' ?>><?= e($creator['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
                <button type="button" class="btn btn-outline" onclick="exportTransactionsCSV()"><i class="fas fa-download"></i> Exporter CSV</button>
            </form>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= e($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= e($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="transactions-table-container">
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Créatrice</th>
                    <th>Donateur</th>
                    <th>Montant (€)</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $txn): ?>
                    <tr>
                        <td><?= formatDate($txn['created_at']); ?></td>
                        <td><?= e($txn['creator_name']); ?></td>
                        <td><?= e($txn['donor_name']); ?></td>
                        <td><?= formatAmount($txn['amount']); ?></td>
                        <td><?= e($txn['type']); ?></td>
                        <td><?= e($txn['message'] ?? ''); ?></td>
                        <td>
                            <a href="/admin/transactions/view/<?= $txn['id'] ?>" class="btn btn-view" title="Voir le détail"><i class="fas fa-eye"></i></a>
                            <form action="/admin/transactions/anonymize" method="POST" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <input type="hidden" name="txn_id" value="<?= $txn['id'] ?>">
                                <button type="submit" class="btn btn-delete" title="Anonymiser"><i class="fas fa-user-secret"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($totalPages > 1): ?>
            <?php $pageTitle = 'Transactions'; ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>" class="btn btn-outline">&laquo; Précédent</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="btn btn-outline <?= $i === $page ? 'active' : '' ?>"> <?= $i ?> </a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?>" class="btn btn-outline">Suivant &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.transactions-manager {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}
.filters-form {
    display: flex;
    gap: 1rem;
    align-items: center;
}
.transactions-table-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-x: auto;
}
.transactions-table {
    width: 100%;
    border-collapse: collapse;
}
.transactions-table th,
.transactions-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}
.transactions-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}
.btn {
    padding: 0.5rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.btn-view {
    background: none;
    color: #2ecc71;
}
.btn-delete {
    background: none;
    color: #e74c3c;
}
.btn:hover {
    background: #f8f9fa;
}
.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
}
.pagination .btn-outline {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    background: #fff;
    color: #2c3e50;
}
.pagination .btn-outline:hover,
.pagination .btn-outline.active {
    background: #3498db;
    border-color: #3498db;
    color: #fff;
}
@media (max-width: 1200px) {
    .filters-form {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }
}
</style>

<script>
function exportTransactionsCSV() {
    window.location.href = '/admin/transactions/export-csv' + window.location.search;
}
</script>

