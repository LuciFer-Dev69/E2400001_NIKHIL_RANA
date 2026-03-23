document.addEventListener('DOMContentLoaded', function () {
    const wishlistToggle = document.getElementById('wishlist-toggle');
    const wishlistPopover = document.getElementById('wishlist-popover');

    if (!wishlistToggle || !wishlistPopover) return;

    wishlistToggle.addEventListener('click', function (e) {
        // Fetch the latest wishlist when opened
        if (wishlistPopover.style.display === 'block') {
            fetchWishlist();
        }
    });

    // Optionally fetch on load to show a badge count
    fetchWishlist();

    async function fetchWishlist() {
        try {
            const baseUrl = window.SkillEduConfig ? window.SkillEduConfig.baseUrl : '/EMS1/';
            const response = await fetch(`${baseUrl}api/wishlist_api.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get' })
            });
            const data = await response.json();

            if (data.success) {
                renderWishlist(data.wishlist, baseUrl);
            }
        } catch (error) {
            console.error('Wishlist fetch error:', error);
        }
    }

    function renderWishlist(wishlist, baseUrl) {
        if (!wishlist || wishlist.length === 0) {
            wishlistPopover.innerHTML = `
                <h4 style="margin-bottom: 15px; font-size: 16px;">Wishlist</h4>
                <p style="font-size: 13px; color: #6a6f73; text-align: center; padding: 20px 0;">Your wishlist is empty.</p>
                <a href="${baseUrl}courses.php" style="display: block; text-align: center; font-weight: 700; color: var(--primary-color); text-decoration: none; font-size: 14px;">Explore courses</a>
            `;
            return;
        }

        let html = `<h4 style="margin-bottom: 15px; font-size: 16px;">Wishlist (${wishlist.length})</h4>`;

        wishlist.forEach(item => {
            html += `
                <a href="${baseUrl}course_details.php?id=${item.id}" class="search-result-item" style="border-bottom: 1px solid #f1f3f5; padding-bottom: 12px; margin-bottom: 12px; display: flex; gap: 14px; text-decoration: none;">
                    <img src="${baseUrl}assets/img/courses/${item.thumbnail || 'default.jpg'}" alt="${item.title}" style="width: 44px; height: 44px; object-fit: cover; border-radius: 6px; flex-shrink: 0;" onerror="this.src='https://via.placeholder.com/40x40'">
                    <div style="flex: 1; overflow: hidden; display: flex; flex-direction: column; gap: 2px;">
                        <div style="font-size: 14px; font-weight: 700; color: #1c1d1f; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.title}</div>
                        <div style="font-size: 12px; color: #6a6f73;">By ${item.instructor_name}</div>
                        <div style="font-size: 13px; font-weight: 700; color: #1c1d1f;">$${parseFloat(item.price).toFixed(2)}</div>
                    </div>
                </a>
            `;
        });

        html += `<a href="${baseUrl}courses.php?filter=wishlist" style="display: block; text-align: center; font-weight: 700; color: #1c1d1f; text-decoration: none; font-size: 14px; padding-top: 10px;">View full wishlist</a>`;

        wishlistPopover.innerHTML = html;
    }
});
