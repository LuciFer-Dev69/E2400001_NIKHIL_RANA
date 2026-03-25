        </div> <!-- End .portal-content -->
    </main> <!-- End .portal-main -->
</div> <!-- End .admin-shell -->

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initial Theme Icon Set
        const savedTheme = localStorage.getItem('skilledu_theme');
        const icon = document.getElementById('theme-icon');
        if (savedTheme === 'dark' && icon) {
            icon.className = 'fas fa-sun';
        }
    });

    // Sidebar Active State styling is handled by CSS (inheriting from admin.css)
</script>

</body>
</html>
