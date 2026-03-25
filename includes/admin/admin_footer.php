        </div> <!-- End .portal-content -->
    </main> <!-- End .portal-main -->
</div> <!-- End .admin-shell -->

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Handle User Menu Popover
        const userMenuBtn = document.getElementById('user-menu-toggle');
        const userMenuPop = document.getElementById('user-menu-popover');
        
        if(userMenuBtn && userMenuPop) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isVisible = userMenuPop.style.visibility === 'visible';
                userMenuPop.style.visibility = isVisible ? 'hidden' : 'visible';
                userMenuPop.style.opacity = isVisible ? '0' : '1';
                userMenuPop.style.transform = isVisible ? 'translateY(10px)' : 'translateY(0)';
            });
            
            document.addEventListener('click', (e) => {
                if(!userMenuBtn.contains(e.target) && !userMenuPop.contains(e.target)) {
                    userMenuPop.style.visibility = 'hidden';
                    userMenuPop.style.opacity = '0';
                    userMenuPop.style.transform = 'translateY(10px)';
                }
            });
        }
        
        // Initial Theme Icon Set
        const savedTheme = localStorage.getItem('skilledu_theme');
        const icon = document.getElementById('theme-icon');
        if (savedTheme === 'dark' && icon) {
            icon.className = 'fas fa-sun';
        }
    });

    // Helper specific to formatting numbers dynamically if needed by admin scripts
    window.formatCurrency = function(amount) {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
    }
</script>

</body>
</html>
