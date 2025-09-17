// Sidebar toggle for dashboard

document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.dashboard-sidebar');
    const main = document.querySelector('.dashboard-main');
    const toggleBtn = document.getElementById('sidebar-toggle');
    if (!sidebar || !main || !toggleBtn) return;

    toggleBtn.addEventListener('click', function () {
        document.body.classList.toggle('sidebar-collapsed');
    });
});
