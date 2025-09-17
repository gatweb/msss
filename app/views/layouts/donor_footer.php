        </div>
    </main>

    <script>
    // Toggle sidebar
    document.getElementById('sidebar-toggle').addEventListener('click', function() {
        document.querySelector('.donor-sidebar').classList.toggle('collapsed');
        document.querySelector('.donor-main').classList.toggle('expanded');
    });

    // Recherche en direct
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            // TODO: Implémenter la recherche en direct
            console.log('Recherche:', e.target.value);
        });
    }

    // Gestion des notifications
    function markNotificationAsRead(notifId) {
        fetch(`/donor/notifications/${notifId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    const count = parseInt(badge.textContent) - 1;
                    if (count > 0) {
                        badge.textContent = count;
                    } else {
                        badge.remove();
                    }
                }
            }
        });
    }
    </script>
</body>
</html>
