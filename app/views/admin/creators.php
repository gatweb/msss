
<div class="creators-management">
    <div class="page-header">
        <h1>Gestion des créatrices</h1>
        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="creatorSearch" placeholder="Rechercher une créatrice..." onkeyup="filterCreators()">
            </div>
            <div class="filters">
                <select onchange="filterByStatus(this.value)">
                    <option value="all">Tous les statuts</option>
                    <option value="active">Actives</option>
                    <option value="inactive">Inactives</option>
                    <option value="pending">En attente</option>
                </select>
                <select onchange="sortCreators(this.value)">
                    <option value="recent">Plus récentes</option>
                    <option value="revenue">Revenus</option>
                    <option value="donors">Donateurs</option>
                    <option value="activity">Activité</option>
                </select>
            </div>
            <button class="btn btn-primary" onclick="exportCreatorsData()">
                <i class="fas fa-download"></i>
                Exporter
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

    <div class="creators-table-container">
        <table class="creators-table">
            <thead>
                <tr>
                    <th class="sortable" onclick="sortBy('name')">Créatrice <i class="fas fa-sort"></i></th>
                    <th class="sortable" onclick="sortBy('email')">Email <i class="fas fa-sort"></i></th>
                    <th class="sortable" onclick="sortBy('date')">Inscription <i class="fas fa-sort"></i></th>
                    <th class="sortable" onclick="sortBy('donors')">Donateurs <i class="fas fa-sort"></i></th>
                    <th class="sortable" onclick="sortBy('revenue')">Revenus <i class="fas fa-sort"></i></th>
                    <th class="sortable" onclick="sortBy('packs')">Packs <i class="fas fa-sort"></i></th>
                    <th class="sortable" onclick="sortBy('activity')">Activité <i class="fas fa-sort"></i></th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($creators as $creator): ?>
                    <tr class="creator-row <?= ($creator['status'] ?? 'inactive') === 'active' ? 'active' : 'inactive' ?>" data-creator="<?= htmlspecialchars($creator['name']) ?>">
                        <td class="creator-info">
                            <img src="<?php echo e($creator['profile_pic_url'] ?? '/assets/img/default-avatar.png'); ?>" alt="<?php echo e($creator['name']); ?>">
                            <span><?php echo e($creator['name']); ?></span>
                        </td>
                        <td><?php echo e($creator['email']); ?></td>
                        <td><?php echo formatDate($creator['created_at']); ?></td>
                        <td><?php echo number_format($creator['donation_count']); ?></td>
                        <td><?php echo formatAmount($creator['total_amount']); ?></td>
                        <td><?php echo number_format($creator['pack_count']); ?></td>
                        <td><?php echo $creator['last_donation'] ? formatDate($creator['last_donation']) : '-'; ?></td>
                        <td>
                            <span class="status-badge <?= ($creator['status'] ?? 'inactive') === 'active' ? 'active' : 'inactive'; ?>">
                                <?= ($creator['status'] ?? 'inactive') === 'active' ? 'Actif' : 'Inactif'; ?>
                            </span>
                        </td>
                        <td class="actions">
                            <form action="/admin/creators/toggle-status" method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                <input type="hidden" name="creator_id" value="<?php echo $creator['id']; ?>">
                                <button type="submit" class="btn btn-status" title="<?= ($creator['status'] ?? 'inactive') === 'active' ? 'Désactiver' : 'Activer' ?>">
                                    <i class="fas fa-<?= ($creator['status'] ?? 'inactive') === 'active' ? 'toggle-on' : 'toggle-off' ?>"></i>
                                </button>
                            </form>
                            
                            <button class="btn btn-view" onclick="window.location.href='/creator/<?php echo e($creator['username']); ?>'" title="Voir le profil">
                                <i class="fas fa-eye"></i>
                            </button>
                            
                            <form action="/admin/creators/delete" method="POST" style="display: inline;" onsubmit="return confirmDelete('<?php echo e($creator['name']); ?>');">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                <input type="hidden" name="creator_id" value="<?php echo $creator['id']; ?>">
                                <button type="submit" class="btn btn-delete" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <?php $pageTitle = 'Créatrices'; ?>
        <main class="admin-main">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo ($page - 1); ?>" class="btn btn-outline">&laquo; Précédent</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="btn btn-outline <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo ($page + 1); ?>" class="btn btn-outline">Suivant &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.creators-management {
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

.search-box {
    position: relative;
    width: 300px;
}

.search-box input {
    width: 100%;
    padding: 0.5rem 2.5rem 0.5rem 1rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.search-box i {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #7f8c8d;
}

.creators-table-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow-x: auto;
}

.creators-table {
    width: 100%;
    border-collapse: collapse;
}

.creators-table th,
.creators-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.creators-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}

.creator-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.creator-info img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.creator-row.inactive {
    opacity: 0.7;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-badge.active {
    background: #e1f7e3;
    color: #27ae60;
}

.status-badge.inactive {
    background: #fde8e8;
    color: #e74c3c;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.btn {
    padding: 0.5rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-status {
    background: none;
    color: #3498db;
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
    .creators-table th:nth-child(3),
    .creators-table td:nth-child(3),
    .creators-table th:nth-child(6),
    .creators-table td:nth-child(6),
    .creators-table th:nth-child(7),
    .creators-table td:nth-child(7) {
        display: none;
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .search-box {
        width: 100%;
    }
}
</style>

<script>
// Filtrage et tri
let currentSort = { field: 'date', direction: 'desc' };
let currentFilters = { status: 'all', search: '' };

function filterCreators() {
    currentFilters.search = document.getElementById('creatorSearch').value.toLowerCase();
    applyFilters();
}

function filterByStatus(status) {
    currentFilters.status = status;
    applyFilters();
}

function sortBy(field) {
    if (currentSort.field === field) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.field = field;
        currentSort.direction = 'asc';
    }
    
    updateSortIcons();
    applyFilters();
}

function updateSortIcons() {
    document.querySelectorAll('.sortable i').forEach(icon => {
        icon.className = 'fas fa-sort';
    });
    
    const currentIcon = document.querySelector(`.sortable[onclick="sortBy('${currentSort.field}')"] i`);
    if (currentIcon) {
        currentIcon.className = `fas fa-sort-${currentSort.direction === 'asc' ? 'up' : 'down'}`;
    }
}

function applyFilters() {
    const rows = Array.from(document.querySelectorAll('.creator-row'));
    
    // Filtrage
    rows.forEach(row => {
        const creatorName = row.getAttribute('data-creator').toLowerCase();
        const status = row.getAttribute('data-status');
        const matchesSearch = creatorName.includes(currentFilters.search);
        const matchesStatus = currentFilters.status === 'all' || status === currentFilters.status;
        
        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
    
    // Tri
    const visibleRows = rows.filter(row => row.style.display !== 'none');
    visibleRows.sort((a, b) => {
        const valueA = a.getAttribute(`data-${currentSort.field}`);
        const valueB = b.getAttribute(`data-${currentSort.field}`);
        
        if (currentSort.field === 'revenue') {
            return (parseFloat(valueA) - parseFloat(valueB)) * (currentSort.direction === 'asc' ? 1 : -1);
        }
        
        return valueA.localeCompare(valueB) * (currentSort.direction === 'asc' ? 1 : -1);
    });
    
    const tbody = document.querySelector('.creators-table tbody');
    visibleRows.forEach(row => tbody.appendChild(row));
}

function sortCreators(criteria) {
    switch(criteria) {
        case 'recent':
            sortBy('date');
            break;
        case 'revenue':
            sortBy('revenue');
            break;
        case 'donors':
            sortBy('donors');
            break;
        case 'activity':
            sortBy('activity');
            break;
    }
}

function confirmDelete(creatorName) {
    return confirm(`Êtes-vous sûr de vouloir supprimer le créateur "${creatorName}" ?\n\nCette action est irréversible et supprimera :\n- Son profil\n- Ses packs\n- Ses liens sociaux\n\nLes dons seront anonymisés.`);
}
</script>

