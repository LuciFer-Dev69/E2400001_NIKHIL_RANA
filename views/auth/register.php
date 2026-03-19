<?php include '../../includes/header.php'; ?>

<main class="container py-5 d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card glass-panel p-4 p-lg-5 col-12 col-md-8 col-lg-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold"><span class="text-red">Join</span> SkillStack</h2>
            <p class="text-muted">Choose your role and start your journey today.</p>
        </div>

        <form action="#" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Full Name</label>
                <input type="text" class="form-control rounded-pill" placeholder="Enter your full name" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" class="form-control rounded-pill" placeholder="name@example.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" class="form-control rounded-pill" placeholder="Create a strong password" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">I want to join as a:</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="role" id="roleLearner" value="learner" checked>
                        <label class="form-check-label" for="roleLearner">Learner</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="role" id="roleProvider" value="provider">
                        <label class="form-check-label" for="roleProvider">Training Provider</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-red w-100 rounded-pill py-2 fw-bold">Create Account</button>
        </form>

        <div class="text-center mt-4">
            <p class="small text-muted">Already have an account? <a href="login.php" class="text-red fw-bold">Log in</a></p>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>
