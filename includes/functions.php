<?php
// includes/functions.php - ENHANCED FOR SECURITY

session_start();

/**
 * Generate a CSRF token for forms.
 */
function get_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token.
 */
function validate_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Audit Logging for critical actions.
 */
function log_action($pdo, $action, $details = "")
{
    $user_id = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $action, $details, $ip]);
}

/**
 * Check if a user has a specific role.
 */
function check_role($allowed_roles)
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: ../index.php?error=unauthorized");
        exit();
    }
}

function format_price($amount)
{
    return "RM " . number_format($amount, 2);
}

function generate_transaction_id()
{
    return strtoupper(uniqid('TXN_'));
}

/**
 * Global CSRF Input Field
 */
function csrf_field()
{
    echo '<input type="hidden" name="csrf_token" value="' . get_csrf_token() . '">';
}
?>
