<?php
/**
 * checkout_subscription.php
 * 
 * Modern Subscription Checkout Page
 * Inspired by the reference design.
 */
session_start();

// Mock redirect if not logged in (standard for this project)
if (!isset($_SESSION['user_id'])) {
// For demo purposes, we'll allow viewing if no user session exists
}

// Variables for the page
$page_title = 'Checkout | SkillEdu';
$base_url = "";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Importing fonts from main style.css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/checkout_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="checkout-page">

    <header class="checkout-header">
        <!-- SKILLEDU LOGO FROM PROJECT ASSETS -->
        <a href="index.php" class="logo">Skill<span>Edu</span></a>
        <a href="subscribe.php" style="color: var(--primary-purple); font-weight: 700; text-decoration: none; font-size: 14px;">Cancel</a>
    </header>

    <div class="checkout-container">
        <!-- Main Content -->
        <main class="checkout-main">
            <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 24px;">Checkout to start learning</h1>

            <div class="plan-options">
                <!-- Yearly Plan -->
                <div class="plan-card active" onclick="selectPlan('yearly')">
                    <div class="radio-custom"></div>
                    <div class="save-badge">Save $100</div>
                    <div class="plan-info">
                        <div class="plan-title">Yearly Access</div>
                        <div class="plan-price">$10.00/mo</div>
                        <div class="plan-billed">$120.00 billed yearly</div>
                    </div>
                </div>

                <!-- Monthly Plan -->
                <div class="plan-card" onclick="selectPlan('monthly')">
                    <div class="radio-custom"></div>
                    <div class="plan-info">
                        <div class="plan-title">Monthly Access</div>
                        <div class="plan-price">$19.00/mo</div>
                        <div class="plan-billed">billed monthly</div>
                    </div>
                </div>
            </div>

            <ul class="benefits-list">
                <li class="benefit-item"><i class="fa fa-check"></i> Access to over 26,000 of our top courses in tech, business, and more</li>
                <li class="benefit-item"><i class="fa fa-check"></i> Hands-on learning experiences to build your skills</li>
                <li class="benefit-item"><i class="fa fa-check"></i> Course recommendations to help you start learning faster</li>
            </ul>

            <div class="billing-section">
                <h2 style="font-size: 19px; font-weight: 700; margin-bottom: 16px;">Billing address</h2>
                <div class="form-group">
                    <label class="form-label">Country</label>
                    <div style="position: relative;">
                        <select class="form-input" style="appearance: none; -webkit-appearance: none; padding-right: 40px;">
                            <option value="NP">Nepal</option>
                            <option value="IN">India</option>
                            <option value="US">United States</option>
                            <option value="UK">United Kingdom</option>
                        </select>
                        <i class="fa fa-chevron-down" style="position: absolute; right: 15px; top: 15px; color: #1c1d1f; pointer-events: none;"></i>
                    </div>
                </div>
            </div>

            <div class="payment-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 style="font-size: 19px; font-weight: 700;">Payment Method</h2>
                    <div style="font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 6px;">
                        Secure and encrypted <i class="fa fa-lock" style="font-size: 11px;"></i>
                    </div>
                </div>

                <div class="payment-selector">
                    <div class="radio-custom" style="position: static; border-color: var(--text-dark); background: var(--text-dark);">
                        <div style="width: 8px; height: 8px; background: white; border-radius: 50%; margin: 4px;"></div>
                    </div>
                    <i class="fa fa-credit-card" style="font-size: 18px;"></i>
                    <span style="font-weight: 700; font-size: 14px;">Cards</span>
                    <div style="margin-left: auto; display: flex; gap: 6px;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" height="14" alt="Visa">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" height="14" alt="Mastercard">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" height="14" alt="PayPal">
                    </div>
                </div>

                <div class="card-form">
                    <div class="form-group">
                        <label class="form-label">Card number</label>
                        <div style="position: relative;">
                            <input type="text" class="form-input" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
                            <i class="fa fa-credit-card" style="position: absolute; right: 15px; top: 15px; color: var(--text-muted);"></i>
                        </div>
                    </div>
                    <div class="input-row">
                        <div class="form-group">
                            <label class="form-label">Expiry date</label>
                            <input type="text" class="form-input" id="cardExpiry" placeholder="MM/YY" maxlength="5">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CVC/CVV</label>
                            <input type="text" class="form-input" id="cardCvc" placeholder="CVC" maxlength="4">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Name on card</label>
                        <input type="text" class="form-input" placeholder="Name on card">
                    </div>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 16px;">This card will be stored on your account</p>
                </div>
            </div>
        </main>

        <!-- Sidebar Summary -->
        <aside class="checkout-sidebar">
            <h3 class="summary-title">Summary</h3>
            
            <div id="summaryRows">
                <div class="summary-row">
                    <span id="planNameDisplay">Yearly access:</span>
                    <span id="planPriceDisplay">$120.00/year</span>
                </div>
            </div>

            <div class="summary-total">
                <span>Total:</span>
                <span id="totalPriceDisplay">$120.00/year</span>
            </div>

            <div class="coupon-section">
                <button class="coupon-toggle" onclick="toggleCoupon()">Apply Coupon</button>
                <div id="couponInputWrap" style="display: none; margin-top: 12px;">
                    <div style="display: flex; gap: 8px;">
                        <input type="text" class="form-input" placeholder="Enter coupon" style="padding: 8px 12px; font-size: 14px;">
                        <button class="btn-checkout" style="width: auto; padding: 8px 16px; font-size: 14px;">Apply</button>
                    </div>
                </div>
            </div>

            <div class="disclaimer">
                Cancel anytime by visiting the Subscriptions page in your account.
                <br><br>
                Your subscription will begin today and a charge of <span id="disclaimerPrice">$120.00</span> automatically each year after that until you cancel. By clicking "Start Subscription" you agree to our <a href="#" style="color: var(--primary-purple);">Terms</a> and authorize this recurring charge. No refunds or partial credits except where required by law.
            </div>

            <button class="btn-checkout" onclick="startSubscription()" id="subBtn">
                <i class="fa fa-lock" style="margin-right: 8px; font-size: 13px;"></i> Start Subscription
            </button>
        </aside>
    </div>

    <!-- Success Modal -->
    <div id="successModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 40px; border-radius: 8px; text-align: center; max-width: 400px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div style="width: 60px; height: 60px; background: #cfe8d5; color: #1e8331; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 24px;">
                <i class="fa fa-check"></i>
            </div>
            <h2 style="font-weight: 700; font-size: 22px; margin-bottom: 12px;">Success!</h2>
            <p style="color: var(--text-muted); margin-bottom: 24px;">Your subscription has been activated. You now have full access to everything.</p>
            <button class="btn-checkout" onclick="window.location.href='index.php'">Go to Dashboard</button>
        </div>
    </div>

    <script>
        // State
        let currentPlan = 'yearly';

        // Plan Selection
        function selectPlan(plan) {
            currentPlan = plan;
            
            // Update UI cards
            document.querySelectorAll('.plan-card').forEach(card => card.classList.remove('active'));
            if (plan === 'yearly') {
                document.querySelectorAll('.plan-card')[0].classList.add('active');
                updateSummary('Yearly access:', '$120.00/year', '$120.00');
            } else {
                document.querySelectorAll('.plan-card')[1].classList.add('active');
                updateSummary('Monthly access:', '$19.00/mo', '$19.00');
            }
        }

        function updateSummary(name, price, disc) {
            document.getElementById('planNameDisplay').innerText = name;
            document.getElementById('planPriceDisplay').innerText = price;
            document.getElementById('totalPriceDisplay').innerText = price;
            document.getElementById('disclaimerPrice').innerText = disc;
        }

        // Coupon Toggle
        function toggleCoupon() {
            const wrap = document.getElementById('couponInputWrap');
            wrap.style.display = wrap.style.display === 'none' ? 'block' : 'none';
        }

        // Card Formatting
        const cardInput = document.getElementById('cardNumber');
        if (cardInput) {
            cardInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) formattedValue += ' ';
                    formattedValue += value[i];
                }
                e.target.value = formattedValue.substring(0, 19);
            });
        }

        const expiryInput = document.getElementById('cardExpiry');
        if (expiryInput) {
            expiryInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\//g, '').replace(/[^0-9]/gi, '');
                if (value.length >= 2) {
                    e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
                } else {
                    e.target.value = value;
                }
            });
        }

        // Subscription Logic
        function startSubscription() {
            const btn = document.getElementById('subBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin" style="margin-right: 8px;"></i> Processing...';
            
            // Simulate processing
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'flex';
            }, 2000);
        }
    </script>
</body>
</html>
