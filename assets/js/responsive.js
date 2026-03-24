/**
 * responsive.js
 * 
 * Handles all global responsive interactions:
 * - Mobile Sidebar toggle for Portals
 * - Mobile Navbar toggle for Public Header
 */

document.addEventListener('DOMContentLoaded', () => {

    // 1. PORTAL SIDEBAR TOGGLE
    const portalSidebarBtn = document.getElementById('sidebar-toggle');
    const shell = document.querySelector('.portal-shell');

    if (portalSidebarBtn && shell) {
        // Create overlay if it doesn't exist
        let overlay = document.querySelector('.portal-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'portal-overlay';
            shell.appendChild(overlay);
        }

        portalSidebarBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            shell.classList.toggle('sidebar-open');
        });

        overlay.addEventListener('click', () => {
            shell.classList.remove('sidebar-open');
        });
    }

    // 2. PUBLIC NAVBAR TOGGLE
    const mobileMenuBtn = document.getElementById('mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            navLinks.classList.toggle('mobile-open');
            mobileMenuBtn.classList.toggle('active');
        });
    }

    // Close menus on Esc key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (shell) shell.classList.remove('sidebar-open');
            if (navLinks) navLinks.classList.remove('mobile-open');
        }
    });

    // Handle Window Resize
    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
            if (shell) shell.classList.remove('sidebar-open');
            if (navLinks) navLinks.classList.remove('mobile-open');
        }
    });
});
