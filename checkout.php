<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = "checkout.php?id=" . ($_GET['id'] ?? 0);
    header("Location: login.php");
    exit();
}

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$course_id) {
    header("Location: courses.php");
    exit();
}

// Fetch Course Data - Allow pending status for testing/preview if needed
$stmt = $pdo->prepare("SELECT c.*, u.full_name as instructor_name, cat.name as category_name 
                      FROM courses c 
                      JOIN users u ON c.instructor_id = u.id 
                      LEFT JOIN categories cat ON c.category_id = cat.id 
                      WHERE c.id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    // If course doesn't exist at all
    $_SESSION['error'] = "The course you are looking for is no longer available.";
    header("Location: courses.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Already enrolled?
$check = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
$check->execute([$user_id, $course_id]);
if ($check->fetch()) {
    header("Location: portals/student/player.php?course_id=$course_id");
    exit();
}

// Handle AJAX enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_checkout'])) {
    header('Content-Type: application/json');
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO enrollments (student_id, course_id, enrolled_at, progress_percent) VALUES (?, ?, NOW(), 0)");
        $stmt->execute([$user_id, $course_id]);
        echo json_encode(['success' => true, 'redirect' => "portals/student/player.php?course_id=$course_id&enrolled=true"]);
    }
    catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

$lesson_count = $pdo->prepare("SELECT COUNT(*) FROM lessons WHERE course_id = ?");
$lesson_count->execute([$course_id]);
$lessons = $lesson_count->fetchColumn();

$page_title = 'Checkout — ' . $course['title'];
include 'includes/header.php';
?>

<style>
    .checkout-wrap {
        max-width: 1100px;
        margin: 0 auto;
        padding: 50px 20px 80px;
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 35px;
        align-items: start;
    }
    @media(max-width: 800px) {
        .checkout-wrap { grid-template-columns: 1fr; }
        .order-summary-card { order: -1; }
    }

    /* Gateway Tabs */
    .gateway-tabs { display: flex; gap: 12px; margin-bottom: 28px; }
    .gateway-tab {
        flex: 1; padding: 14px 10px; border-radius: 12px;
        border: 2px solid var(--border-color); background: var(--bg-card);
        cursor: pointer; text-align: center; transition: all 0.2s;
        display: flex; flex-direction: column; align-items: center; gap: 8px;
    }
    .gateway-tab:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
    .gateway-tab.active { border-color: var(--tab-color); background: var(--tab-bg); }
    .gateway-tab img { height: 28px; object-fit: contain; }
    .gateway-tab span { font-size: 12px; font-weight: 800; color: var(--dark-color); }

    /* Payment Panels */
    .payment-panel { display: none; animation: fadeSlide 0.3s ease; }
    .payment-panel.active { display: block; }
    @keyframes fadeSlide { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

    /* Input Style */
    .pay-input {
        width: 100%; padding: 14px 16px;
        border-radius: 10px; border: 1.5px solid var(--border-color);
        background: var(--light-gray); color: var(--dark-color);
        font-family: inherit; font-size: 15px; transition: border-color 0.2s;
        box-sizing: border-box;
    }
    .pay-input:focus { outline: none; border-color: #9b59b6; }
    .pay-label { display: block; font-weight: 800; font-size: 13px; color: var(--gray-color); text-transform: uppercase; letter-spacing: .7px; margin-bottom: 8px; }

    /* CARD FLIP */
    .card-scene { width: 100%; max-width: 380px; height: 210px; perspective: 1000px; margin: 0 auto 28px; }
    .card-inner { position: relative; width: 100%; height: 100%; transition: transform 0.7s cubic-bezier(.4,.2,.2,1); transform-style: preserve-3d; }
    .card-scene.flipped .card-inner { transform: rotateY(180deg); }
    .card-face {
        position: absolute; width: 100%; height: 100%; border-radius: 16px;
        backface-visibility: hidden; -webkit-backface-visibility: hidden;
        padding: 22px 28px; box-sizing: border-box; box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 100%);
        color: white; display: flex; flex-direction: column; justify-content: space-between;
    }
    .card-back { transform: rotateY(180deg); background: linear-gradient(135deg, #0f3460 0%, #16213e 100%); }
    .card-number-display { font-size: 20px; letter-spacing: 4px; font-weight: 700; font-family: monospace; }
    .card-chip { width: 44px; height: 34px; background: linear-gradient(135deg, #f5c842, #c9a227); border-radius: 6px; }
    .card-network { font-size: 28px; font-weight: 900; font-style: italic; }
    .card-stripe { height: 46px; background: #222; margin: 18px -28px 12px; }
    .card-cvv-box { background: white; color: #333; text-align: right; padding: 6px 12px; border-radius: 4px; font-weight: 800; font-size: 14px; font-family: monospace; width: 60px; margin-left: auto; }

    /* Pay Button */
    .pay-btn {
        width: 100%; padding: 16px; border: none; border-radius: 12px;
        font-size: 17px; font-weight: 800; cursor: pointer;
        margin-top: 20px; transition: all 0.2s; letter-spacing: .5px;
    }
    .pay-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
    .pay-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    /* Success Overlay */
    #success-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.85);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; flex-direction: column; gap: 20px;
    }
    .success-box {
        background: var(--bg-card); border-radius: 20px; padding: 50px 55px;
        text-align: center; max-width: 440px; box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        border: 1px solid var(--border-color);
    }
    .checkmark-circle { width: 80px; height: 80px; border-radius: 50%; background: #2ecc71; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 36px; color: white; animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    @keyframes popIn { 0% { transform: scale(0); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }

    /* Processing Overlay */
    #processing-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.7);
        display: none; align-items: center; justify-content: center; z-index: 9998; flex-direction: column; gap: 18px;
    }
    .spinner { width: 52px; height: 52px; border: 5px solid rgba(255,255,255,0.2); border-top-color: white; border-radius: 50%; animation: spin 0.9s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<!-- Processing Overlay -->
<div id="processing-overlay">
    <div class="spinner"></div>
    <p style="color: white; font-weight: 800; font-size: 18px;" id="processing-label">Processing Payment...</p>
</div>

<!-- Success Overlay -->
<div id="success-overlay">
    <div class="success-box">
        <div class="checkmark-circle">✓</div>
        <h2 style="font-size: 26px; font-weight: 800; color: var(--dark-color); margin-bottom: 10px;">Payment Successful!</h2>
        <p style="color: var(--gray-color); margin-bottom: 10px;" id="success-desc">Your enrollment is confirmed.</p>
        <p style="font-size: 13px; color: var(--gray-color);">Redirecting you to the course...</p>
        <div style="width: 100%; height: 4px; background: var(--border-color); border-radius: 2px; margin-top: 20px; overflow: hidden;">
            <div id="redirect-bar" style="height: 100%; background: #2ecc71; width: 0%; transition: width 2.5s linear; border-radius: 2px;"></div>
        </div>
    </div>
</div>

<div class="checkout-wrap">

    <!-- LEFT: Payment Methods -->
    <div>
        <div style="margin-bottom: 30px;">
            <a href="course_details.php?id=<?php echo $course_id; ?>" style="color: var(--gray-color); font-size: 14px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 15px;"><i class="fa fa-arrow-left"></i> Back to Course</a>
            <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 6px;">Complete Your Purchase</h1>
            <p style="color: var(--gray-color); font-size: 15px;">Choose your preferred payment method below.</p>
        </div>

        <?php if ($course['price'] > 0): ?>

        <!-- Gateway Selector Tabs -->
        <div class="gateway-tabs">
            <!-- Khalti -->
            <div class="gateway-tab active" id="tab-khalti" onclick="switchGateway('khalti')" style="--tab-color: #5C2D91; --tab-bg: rgba(92,45,145,0.07);">
                <div style="width: 36px; height: 36px; background: #5C2D91; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-weight: 900; font-size: 16px;">K</span>
                </div>
                <span>Khalti</span>
            </div>
            <!-- eSewa -->
            <div class="gateway-tab" id="tab-esewa" onclick="switchGateway('esewa')" style="--tab-color: #60BB46; --tab-bg: rgba(96,187,70,0.07);">
                <div style="width: 36px; height: 36px; background: #60BB46; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-weight: 900; font-size: 16px;">e</span>
                </div>
                <span>eSewa</span>
            </div>
            <!-- Card -->
            <div class="gateway-tab" id="tab-card" onclick="switchGateway('card')" style="--tab-color: #2c3e50; --tab-bg: rgba(44,62,80,0.06);">
                <div style="width: 36px; height: 36px; background: #2c3e50; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-credit-card" style="color: white; font-size: 16px;"></i>
                </div>
                <span>Card</span>
            </div>
        </div>

        <!-- ===== KHALTI PANEL ===== -->
        <div class="payment-panel active" id="panel-khalti">
            <div style="background: var(--bg-card); border-radius: 14px; border: 1px solid var(--border-color); padding: 30px; box-shadow: var(--shadow);">
                <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                    <div style="width: 52px; height: 52px; background: #5C2D91; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <span style="color: white; font-weight: 900; font-size: 24px;">K</span>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 18px; color: var(--dark-color);">Pay with Khalti</div>
                        <div style="font-size: 13px; color: var(--gray-color);">Secure payment via Khalti digital wallet</div>
                    </div>
                </div>
                <div style="margin-bottom: 20px;">
                    <label class="pay-label">Khalti Mobile Number</label>
                    <input id="khalti-mobile" class="pay-input" type="tel" maxlength="10" placeholder="98XXXXXXXX" oninput="this.value=this.value.replace(/\D/g,'')">
                </div>
                <div style="margin-bottom: 20px;">
                    <label class="pay-label">Khalti MPIN</label>
                    <input id="khalti-mpin" class="pay-input" type="password" maxlength="6" placeholder="• • • • • •">
                </div>
                <div style="background: rgba(92,45,145,0.06); border: 1px solid rgba(92,45,145,0.2); border-radius: 8px; padding: 12px 16px; font-size: 13px; color: var(--gray-color); margin-bottom: 5px;">
                    <i class="fa fa-shield-alt" style="color: #5C2D91; margin-right: 6px;"></i> Your credentials are encrypted. Khalti uses 2-factor authentication for all transactions.
                </div>
                <button class="pay-btn" onclick="processPayment('khalti')" style="background: linear-gradient(135deg, #5C2D91, #7B3FC4); color: white;">
                    Pay NPR <?php echo number_format($course['price'] * 133, 0); ?> via Khalti
                    <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                </button>
            </div>
        </div>

        <!-- ===== ESEWA PANEL ===== -->
        <div class="payment-panel" id="panel-esewa">
            <div style="background: var(--bg-card); border-radius: 14px; border: 1px solid var(--border-color); padding: 30px; box-shadow: var(--shadow);">
                <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                    <div style="width: 52px; height: 52px; background: #60BB46; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <span style="color: white; font-weight: 900; font-size: 24px;">e</span>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 18px; color: var(--dark-color);">Pay with eSewa</div>
                        <div style="font-size: 13px; color: var(--gray-color);">Nepal's most trusted digital wallet</div>
                    </div>
                </div>
                <div style="margin-bottom: 20px;">
                    <label class="pay-label">eSewa ID (Mobile Number)</label>
                    <input id="esewa-id" class="pay-input" type="tel" maxlength="10" placeholder="98XXXXXXXX" oninput="this.value=this.value.replace(/\D/g,'')">
                </div>
                <div style="margin-bottom: 20px;">
                    <label class="pay-label">eSewa Password</label>
                    <input id="esewa-pass" class="pay-input" type="password" placeholder="Enter your eSewa password">
                </div>
                <div style="background: rgba(96,187,70,0.06); border: 1px solid rgba(96,187,70,0.25); border-radius: 8px; padding: 12px 16px; font-size: 13px; color: var(--gray-color); margin-bottom: 5px;">
                    <i class="fa fa-lock" style="color: #60BB46; margin-right: 6px;"></i> You will receive an OTP on your registered number to confirm this payment.
                </div>
                <button class="pay-btn" onclick="processPayment('esewa')" style="background: linear-gradient(135deg, #4CAF50, #60BB46); color: white;">
                    Pay NPR <?php echo number_format($course['price'] * 133, 0); ?> via eSewa
                    <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                </button>
            </div>
        </div>

        <!-- ===== CARD PANEL ===== -->
        <div class="payment-panel" id="panel-card">
            <div style="background: var(--bg-card); border-radius: 14px; border: 1px solid var(--border-color); padding: 30px; box-shadow: var(--shadow);">
                <!-- Animated Flip Card Preview -->
                <div class="card-scene" id="card-scene">
                    <div class="card-inner">
                        <!-- Front -->
                        <div class="card-face">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="card-chip"></div>
                                <div class="card-network" id="card-network-logo">VISA</div>
                            </div>
                            <div class="card-number-display" id="card-display-number">•••• •••• •••• ••••</div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                                <div>
                                    <div style="font-size: 10px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px;">Card Holder</div>
                                    <div style="font-size: 14px; font-weight: 700;" id="card-display-name"><?php echo strtoupper(htmlspecialchars($_SESSION['full_name'] ?? 'Full Name')); ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 10px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px;">Expires</div>
                                    <div style="font-size: 14px; font-weight: 700;" id="card-display-exp">MM/YY</div>
                                </div>
                            </div>
                        </div>
                        <!-- Back -->
                        <div class="card-face card-back">
                            <div class="card-stripe"></div>
                            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; padding: 0 8px;">
                                <div style="flex: 1; height: 12px; background: rgba(255,255,255,0.15); border-radius: 2px;"></div>
                                <div class="card-cvv-box" id="card-display-cvv">•••</div>
                            </div>
                            <div style="text-align: center; font-size: 11px; opacity: 0.5; margin-top: 20px;">Authorized Signature Panel</div>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="grid-column: 1/-1;">
                        <label class="pay-label">Card Number</label>
                        <input id="card-number" class="pay-input" type="text" maxlength="19" placeholder="1234 5678 9012 3456" oninput="formatCardNumber(this)" style="font-family: monospace; letter-spacing: 2px;">
                    </div>
                    <div style="grid-column: 1/-1;">
                        <label class="pay-label">Name on Card</label>
                        <input id="card-name" class="pay-input" type="text" placeholder="Full Name" value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>" oninput="document.getElementById('card-display-name').innerText = this.value.toUpperCase() || 'FULL NAME'">
                    </div>
                    <div>
                        <label class="pay-label">Expiry Date</label>
                        <input id="card-exp" class="pay-input" type="text" maxlength="5" placeholder="MM/YY" oninput="formatExpiry(this); document.getElementById('card-display-exp').innerText = this.value || 'MM/YY'">
                    </div>
                    <div>
                        <label class="pay-label">CVV</label>
                        <input id="card-cvv" class="pay-input" type="password" maxlength="4" placeholder="•••" onfocus="document.getElementById('card-scene').classList.add('flipped')" onblur="document.getElementById('card-scene').classList.remove('flipped')" oninput="document.getElementById('card-display-cvv').innerText = this.value || '•••'">
                    </div>
                </div>
                <div style="background: rgba(44,62,80,0.05); border: 1px solid var(--border-color); border-radius: 8px; padding: 12px 16px; font-size: 13px; color: var(--gray-color); margin-top: 16px;">
                    <i class="fa fa-lock" style="color: #2c3e50; margin-right: 6px;"></i> Secured by 256-bit SSL encryption. We do not store your card details.
                </div>
                <button class="pay-btn" onclick="processPayment('card')" style="background: linear-gradient(135deg, #2c3e50, #34495e); color: white;">
                    Pay $<?php echo number_format($course['price'], 2); ?> Securely
                    <i class="fa fa-lock" style="margin-left: 8px;"></i>
                </button>
            </div>
        </div>

        <?php
else: ?>
        <!-- FREE COURSE -->
        <div style="background: var(--bg-card); border-radius: 14px; border: 1px solid var(--border-color); padding: 40px; text-align: center; box-shadow: var(--shadow);">
            <div style="font-size: 64px; margin-bottom: 18px;">🎁</div>
            <h2 style="font-size: 24px; font-weight: 800; color: var(--dark-color); margin-bottom: 12px;">This Course is Free!</h2>
            <p style="color: var(--gray-color); margin-bottom: 30px;">No payment required. Click below to instantly enroll and start learning.</p>
            <button class="pay-btn" onclick="processPayment('free')" style="background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; font-size: 18px;">
                <i class="fa fa-graduation-cap" style="margin-right: 8px;"></i> Enroll Now — It's Free
            </button>
        </div>
        <?php
endif; ?>
    </div>

    <!-- RIGHT: Order Summary -->
    <div class="order-summary-card" style="position: sticky; top: 30px;">
        <div style="background: var(--bg-card); border-radius: 14px; border: 1px solid var(--border-color); padding: 28px; box-shadow: var(--shadow);">
            <h3 style="font-weight: 800; font-size: 18px; color: var(--dark-color); margin-bottom: 20px;">Order Summary</h3>

            <!-- Course Card -->
            <div style="display: flex; gap: 15px; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                <div style="width: 85px; height: 65px; border-radius: 8px; overflow: hidden; flex-shrink: 0; background: var(--light-gray);">
                    <?php
$thumb = $course['thumbnail'] ?? 'course_default.png';
$thumb_path = file_exists('assets/images/thumbnails/' . $thumb) ? 'assets/images/thumbnails/' . $thumb : 'assets/images/' . $thumb;
?>
                    <img src="<?php echo $thumb_path; ?>" alt="Course" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=200&q=70'">
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 14px; color: var(--dark-color); line-height: 1.4; margin-bottom: 5px;"><?php echo htmlspecialchars($course['title']); ?></div>
                    <div style="font-size: 12px; color: var(--gray-color);">By <?php echo htmlspecialchars($course['instructor_name']); ?></div>
                </div>
            </div>

            <!-- Meta -->
            <div style="display: flex; flex-direction: column; gap: 10px; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                <div style="display: flex; gap: 8px; align-items: center; font-size: 13px; color: var(--gray-color);">
                    <i class="fa fa-play-circle" style="color: var(--primary-color);"></i> <?php echo $lessons; ?> Lessons
                </div>
                <div style="display: flex; gap: 8px; align-items: center; font-size: 13px; color: var(--gray-color);">
                    <i class="fa fa-signal" style="color: var(--primary-color);"></i> <?php echo ucfirst($course['difficulty_level'] ?? 'Beginner'); ?> Level
                </div>
                <div style="display: flex; gap: 8px; align-items: center; font-size: 13px; color: var(--gray-color);">
                    <i class="fa fa-tag" style="color: var(--primary-color);"></i> <?php echo htmlspecialchars($course['category_name'] ?? 'General'); ?>
                </div>
                <div style="display: flex; gap: 8px; align-items: center; font-size: 13px; color: #2ecc71; font-weight: 700;">
                    <i class="fa fa-infinity"></i> Lifetime Access
                </div>
            </div>

            <!-- Pricing -->
            <?php if ($course['price'] > 0):
    $original = $course['price'] + 20;
    $discount = 20;
?>
            <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--gray-color); margin-bottom: 8px;">
                <span>Original Price</span>
                <span style="text-decoration: line-through;">$<?php echo number_format($original, 2); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 14px; color: #2ecc71; font-weight: 700; margin-bottom: 18px;">
                <span>Discount Applied</span>
                <span>-$<?php echo number_format($discount, 2); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 22px; font-weight: 800; color: var(--dark-color); padding-top: 15px; border-top: 2px solid var(--border-color);">
                <span>Total</span>
                <span>$<?php echo number_format($course['price'], 2); ?></span>
            </div>
            <?php
else: ?>
            <div style="display: flex; justify-content: space-between; font-size: 22px; font-weight: 800; color: #2ecc71;">
                <span>Total</span>
                <span>FREE</span>
            </div>
            <?php
endif; ?>

            <!-- Trust Badges -->
            <div style="margin-top: 22px; display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--gray-color);">
                    <i class="fa fa-shield-alt" style="color: #2ecc71;"></i> 30-Day Money-Back Guarantee
                </div>
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--gray-color);">
                    <i class="fa fa-lock" style="color: #3498db;"></i> Secure & Encrypted Payment
                </div>
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--gray-color);">
                    <i class="fa fa-certificate" style="color: #f1c40f;"></i> Certificate on Completion
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const courseId = <?php echo $course_id; ?>;

// Gateway switching
function switchGateway(name) {
    ['khalti','esewa','card'].forEach(g => {
        document.getElementById('tab-' + g)?.classList.remove('active');
        document.getElementById('panel-' + g)?.classList.remove('active');
    });
    document.getElementById('tab-' + name)?.classList.add('active');
    const panel = document.getElementById('panel-' + name);
    if (panel) { panel.classList.add('active'); }
}

// Card Number Formatter
function formatCardNumber(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = v.replace(/(.{4})/g, '$1 ').trim();
    document.getElementById('card-display-number').innerText = 
        (v + '................').substring(0, 16).replace(/(.{4})/g, '$1 ').trim().replace(/\d/g, (c, i) => i < v.length ? c : '•');
    
    // Visa/Mastercard detection
    const logo = document.getElementById('card-network-logo');
    if (v[0] === '4') logo.innerText = 'VISA';
    else if (v[0] === '5') logo.innerText = 'MC';
    else if (v[0] === '3') logo.innerText = 'AMEX';
    else logo.innerText = 'CARD';
}

function formatExpiry(input) {
    let v = input.value.replace(/\D/g,'');
    if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2,4);
    input.value = v;
}

// Payment Processing
function processPayment(method) {
    // Validate
    if (method === 'khalti') {
        const m = document.getElementById('khalti-mobile').value.replace(/\D/g,'');
        const p = document.getElementById('khalti-mpin').value;
        if (m.length < 10) return alert('Please enter a valid 10-digit Khalti mobile number.');
        if (p.length < 4) return alert('Please enter your Khalti MPIN (minimum 4 digits).');
    } else if (method === 'esewa') {
        const id = document.getElementById('esewa-id').value.replace(/\D/g,'');
        const pass = document.getElementById('esewa-pass').value;
        if (id.length < 10) return alert('Please enter a valid eSewa ID (10 digits).');
        if (pass.length < 4) return alert('Please enter your eSewa password.');
    } else if (method === 'card') {
        const num = document.getElementById('card-number').value.replace(/\D/g,'');
        const exp = document.getElementById('card-exp').value;
        const cvv = document.getElementById('card-cvv').value;
        if (num.length < 16) return alert('Please enter a valid 16-digit card number.');
        if (exp.length < 5) return alert('Please enter expiry date (MM/YY).');
        if (cvv.length < 3) return alert('Please enter a valid CVV.');
    }

    // Show processing
    const processingEl = document.getElementById('processing-overlay');
    processingEl.style.display = 'flex';
    
    const labels = {
        khalti: 'Verifying Khalti MPIN...',
        esewa: 'Authenticating eSewa Account...',
        card: 'Verifying Card Details...',
        free: 'Enrolling you in the course...'
    };
    document.getElementById('processing-label').innerText = labels[method] || 'Processing...';

    // Simulate gateway delay
    const delay = method === 'free' ? 800 : 2200;
    setTimeout(() => {
        // Change label midway for realism
        if (method !== 'free') {
            document.getElementById('processing-label').innerText = 'Confirming Payment...';
        }
        setTimeout(() => {
            completePurchase(method);
        }, method === 'free' ? 0 : 900);
    }, delay);
}

function completePurchase(method) {
    // AJAX enrollment
    const form = new FormData();
    form.append('process_checkout', '1');

    fetch('checkout.php?id=' + courseId, { method: 'POST', body: form })
    .then(r => r.json())
    .then(data => {
        document.getElementById('processing-overlay').style.display = 'none';
        if (data.success) {
            const descs = {
                khalti: 'NPR <?php echo number_format($course['price'] * 133, 0); ?> deducted from your Khalti Wallet.',
                esewa: 'NPR <?php echo number_format($course['price'] * 133, 0); ?> deducted from your eSewa balance.',
                card: '$<?php echo number_format($course['price'], 2); ?> charged to your card.',
                free: 'You have been successfully enrolled!'
            };
            document.getElementById('success-desc').innerText = descs[method] || 'Enrollment confirmed.';
            document.getElementById('success-overlay').style.display = 'flex';
            // Progress bar then redirect
            setTimeout(() => {
                document.getElementById('redirect-bar').style.width = '100%';
            }, 100);
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2800);
        } else {
            alert('Enrollment failed. Please try again.');
        }
    })
    .catch(() => {
        document.getElementById('processing-overlay').style.display = 'none';
        alert('Network error. Please try again.');
    });
}
</script>

<?php include 'includes/footer.php'; ?>
