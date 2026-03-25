<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Q&A Messages';
include '../../includes/instructor/instructor_header.php';
?>

<div style="margin-bottom: 25px;">
    <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Q&A / Messages</h1>
    <p style="color: var(--gray-color);">Respond to student questions and manage course discussions.</p>
</div>

<div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); padding: 60px; text-align: center; box-shadow: var(--shadow);">
    <div style="font-size: 60px; margin-bottom: 20px;">🚧</div>
    <h3 style="font-weight: 800; color: var(--dark-color); margin-bottom: 12px; font-size: 22px;">Coming Soon</h3>
    <p style="color: var(--gray-color); max-width: 450px; margin: 0 auto; line-height: 1.7;">
        The Q&A and messaging system is under construction. Soon, you'll be able to reply to student questions, pin important answers, and manage all course discussions from this panel.
    </p>
</div>

<?php include '../../includes/instructor/instructor_footer.php'; ?>
