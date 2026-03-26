            </div> <!-- End .portal-content -->
        </main> <!-- End .portal-main -->
</div> <!-- End .admin-shell -->

<!-- Toast Container -->
<div id="toast-container"></div>

<script src="<?php echo $root; ?>assets/js/notifications.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initial Theme Icon Set
        const savedTheme = localStorage.getItem('skilledu_theme');
        const icon = document.getElementById('theme-icon');
        if (savedTheme === 'dark' && icon) {
            icon.className = 'fas fa-sun';
        }
    });
</script>

</body>
</html>
