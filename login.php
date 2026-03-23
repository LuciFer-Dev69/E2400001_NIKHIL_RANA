<?php

session_start();
include 'includes/header.php';

?>

<div class="container">
    <div class="auth-container">
        <h2>Log in to SkillEdu</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: #fff2f2; border: 1px solid #ff0000; color: #ff0000; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px;">
                <i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['error'];
    unset($_SESSION['error']); ?>
            </div>
        <?php
endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: #f3fcf4; border: 1px solid #10b981; color: #10b981; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px;">
                <i class="fa fa-check-circle"></i> <?php echo $_SESSION['success'];
    unset($_SESSION['success']); ?>
            </div>
        <?php
endif; ?>

        <form action="process_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-gradient" style="width: 100%; border: none;">Log in</button>
        </form>
        <div class="auth-footer">
            Don't have an account? <a href="signup.php">Sign up</a>
            <br><br>
            <a href="#" style="color: var(--gray-color); font-weight: 400;">Forgot password?</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
