/* assets/js/Nikhil.js - PROFESSIONAL ENHANCEMENTS */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize Toast System (SweetAlert2)
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
        Toast.fire({ icon, title });
    };

    // 2. Loading Bar Simulation (Professional feel)
    const loadingBar = document.createElement('div');
    loadingBar.id = 'loading-bar';
    document.body.appendChild(loadingBar);

    window.addEventListener('beforeunload', function () {
        loadingBar.style.width = '100%';
    });

    // 3. Bootstrap Validation
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                window.showToast('error', 'Please fill all required fields');
            } else {
                // Show loading on valid submit
                loadingBar.style.width = '100%';
            }
            form.classList.add('was-validated')
        }, false)
    });

    // 4. URL Message Handler (Success/Error from PHP)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('msg')) {
        const msg = urlParams.get('msg');
        if (msg === 'success' || msg === 'added' || msg === 'updated' || msg === 'deleted') {
            window.showToast('success', 'Action completed successfully');
        } else if (msg === 'error') {
            window.showToast('error', 'An error occurred');
        }
    }
});
