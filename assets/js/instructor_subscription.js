/* assets/js/instructor_subscription.js */

document.addEventListener('DOMContentLoaded', () => {
    const billingToggle = document.getElementById('billingToggle');
    const cards = document.querySelectorAll('.pricing-card');
    const modal = document.getElementById('comparisonModal');

    // 1. Pricing Toggle Logic
    const plans = {
        pro: { monthly: 29, yearly: 290 },
        enterprise: { monthly: 99, yearly: 990 }
    };

    if (billingToggle) {
        billingToggle.addEventListener('change', () => {
            const isYearly = billingToggle.checked;
            updatePrices(isYearly);
        });
    }

    function updatePrices(isYearly) {
        document.querySelectorAll('.plan-price').forEach(priceEl => {
            const plan = priceEl.dataset.plan;
            if (plan && plans[plan]) {
                const amount = isYearly ? plans[plan].yearly : plans[plan].monthly;
                const period = isYearly ? '/year' : '/mo';

                priceEl.style.opacity = '0';
                setTimeout(() => {
                    priceEl.innerHTML = `$${amount} <span>${period}</span>`;
                    priceEl.style.opacity = '1';
                }, 200);
            }
        });

        document.querySelectorAll('.billing-period-label').forEach(label => {
            label.innerText = isYearly ? 'Billed annually' : 'Billed monthly';
        });
    }

    // 2. 3D Tilt Effect
    cards.forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;

            card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px) scale(1.02)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = `rotateX(0) rotateY(0) translateY(0) scale(1)`;
        });
    });

    // 3. Entry Animations (Intersection Observer)
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('animate-in');
                }, index * 150);
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => observer.observe(card));
});

// 4. Modal Controller
function openComparisonModal() {
    const modal = document.getElementById('comparisonModal');
    if (modal) {
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('active'), 10);
        document.body.style.overflow = 'hidden';
    }
}

function closeComparisonModal() {
    const modal = document.getElementById('comparisonModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

function selectInstructorPlan(planName) {
    const btn = event.target;
    const originalText = btn.innerText;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

    setTimeout(() => {
        alert(`Request to upgrade to ${planName} sent successfully! Our team will contact you for payment details.`);
        btn.disabled = false;
        btn.innerText = originalText;
    }, 1500);
}
