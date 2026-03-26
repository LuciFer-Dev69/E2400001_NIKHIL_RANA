/**
 * global-sync.js
 * 
 * Handles real-time synchronization of user state across multiple tabs
 * using the BroadcastChannel API. 
 */

(function () {
    const syncChannel = new BroadcastChannel('skilledu_sync');

    // 1. LISTEN FOR REMOTE CHANGES
    syncChannel.onmessage = (event) => {
        const { type, payload } = event.data;

        switch (type) {
            case 'PROFILE_UPDATE':
                updateNavbarUI(payload);
                break;
            case 'THEME_UPDATE':
                updateThemeUI(payload);
                break;
            // Add more cases as needed (e.g., CART_UPDATE)
        }
    };

    // 2. DOM UPDATERS
    function updateNavbarUI(data) {
        if (!data) return;

        // Update Full Name
        const nameEl = document.getElementById('navbar-user-name');
        if (nameEl && data.full_name) {
            nameEl.innerText = data.full_name;
        }

        // Update Email
        const emailEl = document.getElementById('navbar-user-email');
        if (emailEl && data.email) {
            emailEl.innerText = data.email;
        }

        // Update Initials
        if (data.full_name) {
            const initials = data.full_name
                .split(' ')
                .map(n => n[0])
                .join('')
                .toUpperCase()
                .substring(0, 2);

            const initialsEls = [
                document.getElementById('navbar-user-initials'),
                document.getElementById('navbar-dropdown-initials')
            ];

            initialsEls.forEach(el => {
                if (el) el.innerText = initials;
            });
        }
    }

    function updateThemeUI(theme) {
        if (!theme) return;
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('skilledu_theme', theme);

        // Update moon/sun icon if it exists
        const themeIcon = document.getElementById('theme-icon');
        if (themeIcon) {
            themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'far fa-moon';
        }
    }

    // 3. EXPOSE GLOBAL SYNC HELPERS
    window.SkillEduSync = {
        broadcastProfileUpdate: (data) => {
            syncChannel.postMessage({ type: 'PROFILE_UPDATE', payload: data });
        },
        broadcastThemeUpdate: (theme) => {
            syncChannel.postMessage({ type: 'THEME_UPDATE', payload: theme });
        }
    };

    // 4. OVERRIDE GLOBAL THEME TOGGLE (if exists)
    // We wrap the existing function to ensure it still runs its 
    // page-specific logic (like refreshing charts) while we broadcast.
    window.addEventListener('load', () => {
        const originalToggleTheme = window.toggleTheme;
        if (typeof originalToggleTheme === 'function') {
            window.toggleTheme = function (e) {
                // Let the original function handle the local toggle & charts
                originalToggleTheme(e);

                // Now broadcast the new state
                const newTheme = document.documentElement.getAttribute('data-theme');
                if (window.SkillEduSync) {
                    window.SkillEduSync.broadcastThemeUpdate(newTheme);
                }
            };
        }
    });

    console.log('SkillEdu Global Sync Service Initialized');
})();
