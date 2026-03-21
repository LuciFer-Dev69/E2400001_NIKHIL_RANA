<?php include '../includes/header.php'; ?>

<!-- Instructor Hero -->
<section class="teach-hero py-5 reveal-on-scroll" style="background: var(--gradient-main); min-height: 500px; display: flex; align-items: center;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-3 fw-bold mb-4" style="font-family: 'Outfit';">
                    Come teach <br> <span class="text-red">with us</span>
                </h1>
                <p class="lead text-muted mb-4 pe-lg-5">
                    Become an instructor and change lives — including your own. Share your knowledge with millions of learners around the world.
                </p>
                <a href="<?php echo (string)($base_path ?? ''); ?>views/auth/register.php" class="btn btn-dark btn-lg px-5 py-3 fw-bold">Get Started Today</a>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="position-relative">
                    <div class="glass-panel rose-placeholder text-center p-4" style="border-radius: 20px;">
                         <div class="d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                            <i class="bi bi-person-video3 display-4 opacity-25 text-red"></i>
                         </div>
                         <h4 class="fw-bold">Your Classroom Awaits</h4>
                         <p class="text-muted small">We provide the tools and platform; you provide the expertise.</p>
                    </div>
                    <!-- Decorative element -->
                    <div class="position-absolute top-0 start-0 translate-middle p-4 bg-red rounded-circle shadow-lg" style="z-index:-1; opacity: 0.1; width: 200px; height: 200px;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Teach Section -->
<section class="py-5 bg-transparent">
    <div class="container py-lg-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">So many reasons to <span class="text-red">start</span></h2>
        </div>
        
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-3">
                    <i class="bi bi-bullseye text-red display-5 mb-3"></i>
                    <h5 class="fw-bold">Teach your way</h5>
                    <p class="text-muted small">Publish the course you want, in the way you want, and always have control of your own content.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <i class="bi bi-people text-red display-5 mb-3"></i>
                    <h5 class="fw-bold">Inspire learners</h5>
                    <p class="text-muted small">Teach what you know and help learners explore their interests, gain new skills, and advance their careers.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <i class="bi bi-wallet2 text-red display-5 mb-3"></i>
                    <h5 class="fw-bold">Get rewarded</h5>
                    <p class="text-muted small">Expand your professional network, build your expertise, and earn money on each paid enrollment.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="py-4 bg-red text-white">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-4">
                <h3 class="fw-bold mb-0">62M</h3>
                <p class="x-small mb-0 text-white-50 uppercase fw-bold">Learners</p>
            </div>
            <div class="col-4 border-start border-end border-white-50">
                <h3 class="fw-bold mb-0">75+</h3>
                <p class="x-small mb-0 text-white-50 uppercase fw-bold">Languages</p>
            </div>
            <div class="col-4">
                <h3 class="fw-bold mb-0">800M</h3>
                <p class="x-small mb-0 text-white-50 uppercase fw-bold">Enrollments</p>
            </div>
        </div>
    </div>
</section>

<!-- Roadmap Section -->
<section class="py-5 bg-transparent">
    <div class="container py-lg-5">
        <h2 class="fw-bold text-center mb-5">How to <span class="text-red">begin</span></h2>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-0 roadmap-row">
                    <!-- Step 1 -->
                    <div class="col-md-4 text-center p-4 border-end">
                        <div class="step-num mb-3">1</div>
                        <h6 class="fw-bold">Plan your curriculum</h6>
                        <p class="text-muted x-small">Define your audience and learning objectives. We help you structure your course for maximum impact.</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="col-md-4 text-center p-4 border-end">
                        <div class="step-num mb-3">2</div>
                        <h6 class="fw-bold">Record your video</h6>
                        <p class="text-muted x-small">Use basic gear like a smartphone or DSLR. Our team provides quality reviews to ensure your videos shine.</p>
                    </div>
                    <!-- Step 3 -->
                    <div class="col-md-4 text-center p-4">
                        <div class="step-num mb-3">3</div>
                        <h6 class="fw-bold">Launch your course</h6>
                        <p class="text-muted x-small">Build your first course and publish it to our global marketplace. Start earning and inspiring today.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 text-center reveal-on-scroll">
    <div class="container py-lg-5">
        <h2 class="fw-bold mb-4">You have the knowledge. We have the platform.</h2>
        <a href="<?php echo (string)($base_path ?? ''); ?>views/auth/register.php" class="btn btn-red btn-lg px-5 py-3 fw-bold">Sign up as an Instructor</a>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
