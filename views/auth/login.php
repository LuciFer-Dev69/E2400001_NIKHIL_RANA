<?php include '../../includes/header.php'; ?>

<main class="container py-5 d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card glass-panel p-4 p-lg-5 col-12 col-md-8 col-lg-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Welcome <span class="text-red">Back</span></h2>
            <p class="text-muted">Log in to your SkillStack account.</p>
        </div>

        <form action="#" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" class="form-control rounded-pill" placeholder="name@example.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" class="form-control rounded-pill" placeholder="Your password" required>
            </div>
            <div class="mb-4 d-flex justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label small" for="remember">Remember me</label>
                </div>
                <a href="#" class="small text-red text-decoration-none">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-red w-100 rounded-pill py-2 fw-bold">Log In</button>
        </form>

        <div class="text-center mt-4">
            <p class="small text-muted">Don't have an account? <a href="register.php" class="text-red fw-bold">Sign up</a></p>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>
