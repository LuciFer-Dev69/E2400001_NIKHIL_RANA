/* Search & Marketplace - Aditya Kumar Jaiwal */

document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.nav-tabs-eduskill .nav-link');
    const grids = document.querySelectorAll('.course-category-grid');
    const categoryTitle = document.querySelector('#current-category-name');

    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();

            // Remove active from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            // Add active to clicked tab
            tab.classList.add('active');

            // Hide all grids
            grids.forEach(g => g.classList.add('d-none'));

            // Show target grid
            const targetId = tab.getAttribute('data-target');
            const targetGrid = document.querySelector(targetId);
            if (targetGrid) {
                targetGrid.classList.remove('d-none');
            }

            // Update category link title
            if (categoryTitle) {
                categoryTitle.textContent = tab.textContent;
            }
        });
    });
});
