<?php

session_start();
include 'includes/header.php';

?>

<div class="container">
    <div class="auth-container">
        <h2>Sign up and start learning</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: #fff2f2; border: 1px solid #ff0000; color: #ff0000; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px;">
                <i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['error'];
    unset($_SESSION['error']); ?>
            </div>
        <?php
endif; ?>
        
        <div class="role-selector">
            <div class="role-btn active" id="studentRole">Student</div>
            <div class="role-btn" id="instructorRole">Instructor</div>
        </div>

        <form action="process_signup.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="role" id="roleInput" value="student">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Full Name" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <div id="instructorFields" style="display: none;">
                <div class="form-group">
                    <label for="expertise">Expertise / Specialty</label>
                    <input type="text" id="expertise" name="expertise" placeholder="e.g. Full Stack Developer, Yoga Instructor">
                    <p style="font-size: 11px; color: var(--gray-color); margin-top: 5px;">What is your primary teaching area?</p>
                </div>
                <div class="form-group">
                    <label for="bio">Short Bio</label>
                    <textarea id="bio" name="bio" placeholder="Tell students about yourself..." rows="3" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 4px; font-family: inherit; font-size: 14px;"></textarea>
                </div>
                <div class="form-group">
                    <label for="verification_doc">Professional Certificate / CV (PDF or Image)</label>
                    <input type="file" id="verification_doc" name="verification_doc" accept=".pdf,image/*">
                    <p style="font-size: 11px; color: var(--gray-color); margin-top: 5px;">Required for instructor verification.</p>
                </div>
            </div>
            <div class="form-group">
                <label style="font-weight: 400; font-size: 13px;">
                    <input type="checkbox" style="width: auto; margin-right: 10px;" checked> 
                    Send me special offers, personalized recommendations, and learning tips.
                </label>
            </div>
            <button type="submit" class="btn btn-gradient" style="width: 100%; border: none;">Sign up</button>
        </form>

        <div class="auth-footer">
            By signing up, you agree to our <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a>.
            <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--border-color);">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
</div>

<script>
    const studentRole = document.getElementById('studentRole');
    const instructorRole = document.getElementById('instructorRole');
    const roleInput = document.getElementById('roleInput');

    const instructorFields = document.getElementById('instructorFields');
    const verificationInput = document.getElementById('verification_doc');
    const expertiseInput = document.getElementById('expertise');

    studentRole.addEventListener('click', () => {
        studentRole.classList.add('active');
        instructorRole.classList.remove('active');
        roleInput.value = 'student';
        instructorFields.style.display = 'none';
        verificationInput.required = false;
        expertiseInput.required = false;
    });

    instructorRole.addEventListener('click', () => {
        instructorRole.classList.add('active');
        studentRole.classList.remove('active');
        roleInput.value = 'instructor';
        instructorFields.style.display = 'block';
        verificationInput.required = true;
        expertiseInput.required = true;
    });
</script>

<?php include 'includes/footer.php'; ?>
