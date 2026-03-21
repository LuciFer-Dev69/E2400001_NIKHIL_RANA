/* assets/js/Nikhil.js - PROFESSIONAL ENHANCEMENTS & CORE LOGIC */

document.addEventListener('DOMContentLoaded', function () {
    // --- 1. PROFESSIONAL TOAST SYSTEM (SweetAlert2) ---
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    window.showToast = function (icon, title) {
        if (typeof Swal !== 'undefined') {
            Toast.fire({ icon, title });
        } else {
            console.log(`Toast: ${icon} - ${title}`);
        }
    };

    // --- 2. LOADING BAR SIMULATION ---
    const loadingBar = document.createElement('div');
    loadingBar.id = 'loading-bar';
    document.body.appendChild(loadingBar);

    window.addEventListener('beforeunload', function () {
        loadingBar.style.width = '100%';
    });

    // --- 3. BOOTSTRAP FORM VALIDATION ---
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                window.showToast('error', 'Please fill all required fields');
            } else {
                loadingBar.style.width = '100%';
            }
            form.classList.add('was-validated')
        }, false)
    });

    // --- 4. URL MESSAGE HANDLER (Success/Error from PHP) ---
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('msg')) {
        const msg = urlParams.get('msg');
        if (msg === 'success' || msg === 'added' || msg === 'updated' || msg === 'deleted') {
            window.showToast('success', 'Action completed successfully');
        } else if (msg === 'error') {
            window.showToast('error', 'An error occurred');
        }
    }

    // --- 5. CORE UI LOGIC (SkillStack) ---
    const exploreBtn = document.querySelector('.mega-menu-trigger > a');
    const megaMenu = document.querySelector('.mega-menu');

    if (exploreBtn) {
        exploreBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            megaMenu.classList.toggle('show-menu');
        });
    }

    // Dynamic Mega Menu Category Switching
    const menuCats = document.querySelectorAll('.menu-cat');
    const subContents = document.querySelectorAll('.sub-menu-content');

    menuCats.forEach(cat => {
        cat.addEventListener('mouseenter', () => {
            menuCats.forEach(c => c.classList.remove('active'));
            cat.classList.add('active');

            subContents.forEach(sub => sub.classList.add('d-none'));

            const targetId = cat.getAttribute('data-target');
            const targetSub = document.querySelector(targetId);
            if (targetSub) {
                targetSub.classList.remove('d-none');
            }
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (megaMenu && megaMenu.classList.contains('show-menu')) {
            if (!megaMenu.contains(e.target) && !exploreBtn.contains(e.target)) {
                megaMenu.classList.remove('show-menu');
            }
        }
    });

    // Courses Showcase Tab Switching
    const courseTabs = document.querySelectorAll('.nav-tabs-eduskill .nav-link');
    const courseGrids = document.querySelectorAll('.course-category-grid');
    const categoryNameSpan = document.getElementById('current-category-name');

    courseTabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();

            courseTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const targetId = tab.getAttribute('data-target');
            courseGrids.forEach(grid => {
                grid.classList.add('d-none');
                if (grid.id === targetId.replace('#', '')) {
                    grid.classList.remove('d-none');
                }
            });

            if (categoryNameSpan) {
                categoryNameSpan.textContent = tab.textContent;
            }
        });
    });

    // --- 6. NAV-SCROLL EFFECT ---
    const nav = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });

    // --- 7. SCROLL REVEAL SYSTEM ---
    const revealOptions = {
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px"
    };

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, revealOptions);

    document.querySelectorAll('.reveal-on-scroll').forEach(el => {
        revealObserver.observe(el);
    });

    // Slider Scroll Logic (Global)
    window.scrollSlider = function (id, direction) {
        const slider = document.getElementById(id);
        const scrollAmount = 300;
        if (slider) {
            slider.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });
        }
    }
});
