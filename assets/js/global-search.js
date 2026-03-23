document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.querySelector('.search-bar');
    if (!searchForm) return;

    const searchInput = searchForm.querySelector('input');
    let resultsDropdown = document.getElementById('global-search-results');

    if (!resultsDropdown) {
        resultsDropdown = document.createElement('div');
        resultsDropdown.id = 'global-search-results';
        resultsDropdown.className = 'global-search-results';
        searchForm.style.position = 'relative';
        searchForm.appendChild(resultsDropdown);
    }

    let debounceTimer;

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            resultsDropdown.innerHTML = '';
            resultsDropdown.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchSearch(query);
        }, 300);
    });

    async function fetchSearch(query) {
        try {
            const baseUrl = window.SkillEduConfig.baseUrl;
            const response = await fetch(`${baseUrl}api/global_search.php?query=${encodeURIComponent(query)}`);
            const data = await response.json();

            renderResults(data);
        } catch (error) {
            console.error('Global search error:', error);
        }
    }

    function renderResults(data) {
        if (data.length === 0) {
            resultsDropdown.innerHTML = '<div class="search-result-item no-results">No courses found</div>';
        } else {
            resultsDropdown.innerHTML = data.map(item => `
                <a href="${window.location.origin}/EMS1/course_details.php?id=${item.id}" class="search-result-item">
                    <img src="${window.location.origin}/EMS1/assets/img/courses/${item.thumbnail || 'default.jpg'}" alt="${item.title}" onerror="this.src='https://via.placeholder.com/40x40'">
                    <div class="result-info">
                        <div class="result-title">${item.title}</div>
                        <div class="result-instructor">By ${item.instructor_name}</div>
                    </div>
                </a>
            `).join('');
        }
        resultsDropdown.style.display = 'block';
    }

    // Close results when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchForm.contains(e.target)) {
            resultsDropdown.style.display = 'none';
        }
    });
});
