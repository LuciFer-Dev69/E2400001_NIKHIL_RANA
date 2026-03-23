<?php
session_start();
// Specific header for subscription might be needed if user wants top narrow bar
include 'includes/header.php';
?>

<main>
    <!-- Subscription Hero -->
    <section class="sub-hero">
        <div class="container sub-hero-grid" style="display: flex; align-items: center; gap: 60px;">
            <div class="sub-hero-content">
                <h4>Personal Plan</h4>
                <h1>Take your career to the next level</h1>
                <p style="font-size: 18px; color: #2d2f31; margin-bottom: 30px;">Go further at work and in life with subscription access to a collection of top-rated courses in tech, business, and more.</p>
                <button class="btn-sub-primary">Start subscription</button>
                <p style="margin-top: 15px; font-size: 14px; color: #6a6f73;">Starting at $10.00 per month. Cancel anytime.</p>
            </div>
            <div class="sub-hero-image">
                <img src="https://images.squarespace-cdn.com/content/v1/55df6bb1e4b008d758362678/1484196831633-ST2K2RZYW1H3Y9M2VZ00/ke17ZwdGBToddI8pDm48kK6stS_tY3I4D-8Gv9j0o2pZw-zPPgdn4jUwVcJE1ZvWQUxwkmyExglNqGp0IvTJZUJFbgE-7XRK3dMEBRBhUpx_iY5_yidk8F0L_S07I1K5vV-v51vL5v5vU9yM8W_F2a1o1v1v1v1v/ke17ZwdGBToddI8pDm48kK6stS_tY3I4D-8Gv9j0o2pZw-zPPgdn4jUwVcJE1ZvWQUxwkmyExglNqGp0IvTJZUJFbgE-7XRK3dMEBRBhUpx_iY5_yidk8F0L_S07I1K5vV-v51vL5v5vU9yM8W_F2a1o1v1v1v1v" alt="Success" style="border-radius: 8px;">
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="sub-stats">
        <div class="container stats-row">
            <div class="stat-item">
                <h2>26,000+</h2>
                <p>on-demand courses</p>
            </div>
            <div class="stat-item">
                <h2>20,000+</h2>
                <p>practice exercises</p>
            </div>
            <div class="stat-item">
                <h2>4.5 <i class="fa fa-star" style="color: #b4690e; font-size: 24px;"></i></h2>
                <p>average course rating</p>
            </div>
            <div class="stat-item">
                <h2>9,000+</h2>
                <p>top instructors</p>
            </div>
        </div>
    </section>

    <!-- Feature 1 -->
    <section class="sub-feature container">
        <div class="sub-feature-grid">
            <div style="flex: 1;">
                <img src="https://images.squarespace-cdn.com/content/v1/55df6bb1e4b008d758362678/1484196831633-ST2K2RZYW1H3Y9M2VZ00/ke17ZwdGBToddI8pDm48kK6stS_tY3I4D-8Gv9j0o2pZw-zPPgdn4jUwVcJE1ZvWQUxwkmyExglNqGp0IvTJZUJFbgE-7XRK3dMEBRBhUpx_iY5_yidk8F0L_S07I1K5vV-v51vL5v5vU9yM8W_F2a1o1v1v1v1v/ke17ZwdGBToddI8pDm48kK6stS_tY3I4D-8Gv9j0o2pZw-zPPgdn4jUwVcJE1ZvWQUxwkmyExglNqGp0IvTJZUJFbgE-7XRK3dMEBRBhUpx_iY5_yidk8F0L_S07I1K5vV-v51vL5v5vU9yM8W_F2a1o1v1v1v1v" style="width: 100%; border-radius: 8px;">
            </div>
            <div class="sub-feature-content" style="flex: 1;">
                <p style="color: #6a6f73; font-weight: 700; font-size: 14px; margin-bottom: 10px;">Current</p>
                <h2>Cutting-edge skills to keep you sharp</h2>
                <p>Learn confidently with up-to-date courses covering in-demand topics like AI for any role, cloud computing certifications, web development, productivity, leadership, design, digital marketing and more.</p>
            </div>
        </div>
    </section>

    <!-- Feature 2 -->
    <section class="sub-feature container" style="padding-top: 0;">
        <div class="sub-feature-grid">
            <div class="sub-feature-content" style="flex: 1;">
                <p style="color: #6a6f73; font-weight: 700; font-size: 14px; margin-bottom: 10px;">Flexible</p>
                <h2>Freedom to explore and discover</h2>
                <p>Test drive a new subject, switch between courses, or pick and choose the lessons that best fit your needs. Personal Plan gives you the power to control what and how you learn. Plus, use the SkillEdu AI Assistant to get instant answers to your questions while you learn.</p>
            </div>
            <div style="flex: 1;">
                 <div style="background: #f7f9fa; padding: 40px; border-radius: 8px; text-align: center;">
                    <i class="fa fa-tablet-screen-button" style="font-size: 120px; color: #1c1d1f;"></i>
                 </div>
            </div>
        </div>
    </section>

    <!-- Collection Showcase -->
    <section class="sub-collection-section">
        <div class="container">
            <h2>Get a peek at the collection</h2>
            <p style="margin-bottom: 40px; color: #6a6f73;">With thousands of our best-rated courses from top SkillEdu instructors, Personal Plan is your subscription to success. Explore some of the included content below.</p>
            
            <div class="tabs-container">
                <ul class="tabs-list" style="justify-content: center;">
                    <li class="tab-item active">Web Development</li>
                    <li class="tab-item">Data Science</li>
                    <li class="tab-item">IT Certifications</li>
                    <li class="tab-item">Graphic Design</li>
                    <li class="tab-item">Digital Marketing</li>
                </ul>
            </div>

            <div class="course-grid-row" style="justify-content: center;">
                 <div class="course-card-v2">
                    <div class="thumb" style="background: #e4e7ea; display: flex; align-items: center; justify-content: center;"><i class="fa fa-code" style="font-size: 40px;"></i></div>
                    <h4>The Complete Full-Stack Web Development Bootcamp</h4>
                    <p style="font-size: 11px; text-align: left;"><span class="badge" style="background: var(--primary-color); color: white;">Premium</span></p>
                 </div>
                 <div class="course-card-v2">
                    <div class="thumb" style="background: #e4e7ea; display: flex; align-items: center; justify-content: center;"><i class="fab fa-js" style="font-size: 40px;"></i></div>
                    <h4>The Complete JavaScript Course 2026: From Zero to Expert!</h4>
                    <p style="font-size: 11px; text-align: left;"><span class="badge" style="background: var(--primary-color); color: white;">Premium</span></p>
                 </div>
                 <div class="course-card-v2">
                    <div class="thumb" style="background: #e4e7ea; display: flex; align-items: center; justify-content: center;"><i class="fab fa-react" style="font-size: 40px;"></i></div>
                    <h4>The Ultimate React Course 2026: React, Next.js, Redux</h4>
                    <p style="font-size: 11px; text-align: left;"><span class="badge" style="background: var(--primary-color); color: white;">Premium</span></p>
                 </div>
            </div>
            
            <div style="margin-top: 40px;">
                <button class="btn-sub-primary">Start subscription</button>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section container">
        <h2>Choose a plan that works for you</h2>
        <div class="pricing-grid">
            <!-- Personal Plan Card -->
            <div class="pricing-card featured">
                <div class="featured-badge">Best value</div>
                <h3 style="font-size: 24px; margin-bottom: 15px;">Personal Plan</h3>
                <p style="font-size: 13px; color: #6a6f73; margin-bottom: 20px;">For you as an individual</p>
                <div style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">Starting at $10.00 per month</div>
                <p style="font-size: 12px; color: #6a6f73; margin-bottom: 30px;">Billed monthly or annually. Cancel anytime.</p>
                <ul style="list-style: none; padding: 0; text-align: left; font-size: 14px; margin-bottom: 30px;">
                    <li style="margin-bottom: 12px;"><i class="fa fa-check" style="color: green; margin-right: 10px;"></i> 26,000+ top-rated courses</li>
                    <li style="margin-bottom: 12px;"><i class="fa fa-check" style="color: green; margin-right: 10px;"></i> 4,000+ practice exercises</li>
                    <li style="margin-bottom: 12px;"><i class="fa fa-check" style="color: green; margin-right: 10px;"></i> Personalized course recommendations</li>
                    <li style="margin-bottom: 12px;"><i class="fa fa-check" style="color: green; margin-right: 10px;"></i> Offline viewing on mobile app</li>
                </ul>
                <button class="btn-sub-primary" style="width: 100%;">Start subscription</button>
            </div>

            <!-- Individual Course Card -->
            <div class="pricing-card">
                <h3 style="font-size: 24px; margin-bottom: 15px;">Buy individual courses</h3>
                <p style="font-size: 13px; color: #6a6f73; margin-bottom: 20px;">Learn anything</p>
                <div style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">$9.99 - $120.00</div>
                <p style="font-size: 12px; color: #6a6f73; margin-bottom: 30px;">One-time payment. Lifetime access.</p>
                <ul style="list-style: none; padding: 0; text-align: left; font-size: 14px; margin-bottom: 30px;">
                    <li style="margin-bottom: 12px;"><i class="fa fa-check" style="color: green; margin-right: 10px;"></i> 210,000+ professional and personal development courses</li>
                    <li style="margin-bottom: 12px;"><i class="fa fa-check" style="color: green; margin-right: 10px;"></i> Pay as you go</li>
                    <li style="margin-bottom: 12px;"><i class="fa fa-check" style="color: green; margin-right: 10px;"></i> Lifetime access to purchased courses</li>
                </ul>
                <button class="btn btn-secondary" style="width: 100%; border: 1px solid #1c1d1f; border-radius: 4px; padding: 14px;">Explore courses</button>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section container">
        <h2 style="text-align: center; margin-bottom: 40px;">Frequently asked questions</h2>
        <div class="faq-accordion">
            <div class="faq-item">
                <div class="faq-question">What is Personal Plan? <i class="fa fa-chevron-down"></i></div>
                <div class="faq-answer">Personal Plan is a monthly or annual subscription that gives you access to thousands of top-rated courses in tech, business, and more.</div>
            </div>
            <div class="faq-item">
                <div class="faq-question">How is Personal Plan different from buying a course? <i class="fa fa-chevron-down"></i></div>
                <div class="faq-answer">Buying a course gives you lifetime access to that specific course. Personal Plan gives you unlimited access to thousands of courses as long as your subscription is active.</div>
            </div>
            <div class="faq-item">
                <div class="faq-question">How are courses selected for Personal Plan? <i class="fa fa-chevron-down"></i></div>
                <div class="faq-answer">We select courses based on quality, student rating, and demand for the topic.</div>
            </div>
        </div>
    </section>
</main>

<script>
    // Simple FAQ Accordion Logic
    document.querySelectorAll('.faq-question').forEach(item => {
        item.addEventListener('click', () => {
            const answer = item.nextElementSibling;
            const icon = item.querySelector('i');
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
                icon.className = 'fa fa-chevron-down';
            } else {
                answer.style.display = 'block';
                icon.className = 'fa fa-chevron-up';
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
