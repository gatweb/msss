        </div><!-- Fin du content-container -->
    </main>

    <script>
        // Toggle sidebar
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });

        // Responsive sidebar
        function handleResize() {
            if (window.innerWidth < 768) {
                document.body.classList.add('sidebar-collapsed');
            } else {
                document.body.classList.remove('sidebar-collapsed');
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize(); // Initial check
    </script>


