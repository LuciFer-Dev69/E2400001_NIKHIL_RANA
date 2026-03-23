<?php include 'includes/header.php'; ?>

<main>
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Welcome Section -->
        <div class="container welcome-section">
            <div class="welcome-avatar">
                <?php
    $initials = '';
    if (isset($_SESSION['full_name'])) {
        $parts = explode(' ', $_SESSION['full_name']);
        foreach ($parts as $p) {
            if (!empty($p))
                $initials .= strtoupper($p[0]);
        }
    }
    echo substr($initials, 0, 2);
?>
            </div>
            <div class="welcome-text">
                <h1>Welcome back, <?php echo isset($_SESSION['full_name']) ? explode(' ', $_SESSION['full_name'])[0] : 'User'; ?></h1>
                <a href="#">Add occupation and interests</a>
            </div>
        </div>

        <!-- Promo Hero -->
        <div class="container">
            <div class="promo-hero">
                <div class="promo-card">
                    <h2>Courses from $9.99</h2>
                    <p>Start building your learning routine today. Offer ends March 27.</p>
                </div>
            </div>
        </div>
    <?php
else: ?>
        <!-- Hero Slider (Guest) -->
        <section class="hero container">
            <div class="hero-slider-wrapper">
                <button class="slider-btn prev-btn"><i class="fa fa-chevron-left"></i></button>
                <button class="slider-btn next-btn"><i class="fa fa-chevron-right"></i></button>
                
                <div class="slider-container">
                    <!-- Slide 1 -->
                    <div class="slide" style="background: var(--primary-gradient);">
                        <div class="hero-content">
                            <h1>Unlock Your Potential with SkillEdu</h1>
                            <p>Learn the latest technologies from industry experts. Start your journey for just $9.99.</p>
                            <a href="#" class="btn btn-primary" style="background: #1c1d1f;">Explore Courses</a>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="slide" style="background: linear-gradient(135deg, #1fa2ff 0%, #12d8fa 100%);">
                        <div class="hero-content">
                            <h1>Master the Future of AI</h1>
                            <p>Comprehensive courses on Machine Learning, LLMs, and Prompt Engineering.</p>
                            <a href="#" class="btn btn-primary" style="background: #1c1d1f;">View AI Courses</a>
                        </div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="hero-content">
                            <h1>Skills that get you Hired</h1>
                            <p>Join over 1 million students learning around the world.</p>
                            <a href="#" class="btn btn-primary" style="background: #1c1d1f;">Get Started Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php
endif; ?>

    <!-- Featured Skills Slider (New Design) -->
    <section class="featured-skills-slider container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div style="max-width: 400px;">
                <h2 class="section-title" style="margin-bottom: 10px;">Learn <span style="font-style: italic;">essential</span> career and life skills</h2>
                <p style="color: var(--gray-color);">Udemy helps you build in-demand skills fast and advance your career in a changing job market.</p>
            </div>
        </div>

        <div class="skills-grid">
            <!-- Skill 1: Generative AI -->
            <div class="skill-card-vertical">
                <div class="skill-card-img" style="background: linear-gradient(135deg, #e4e7ea 0%, #d1d7dc 100%); position: relative;">
                    <!-- Placeholder for image -->
                    <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; height: 200px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <div style="width: 80%; height: 80%; background: linear-gradient(45deg, #90dfb2, #1fa2ff); border-radius: 4px;"></div>
                    </div>
                </div>
                <div class="skill-card-footer">
                    <h3>Generative AI</h3>
                    <i class="fa fa-chevron-right" style="font-size: 12px; color: #1c1d1f;"></i>
                </div>
            </div>

            <!-- Skill 2: IT Certifications -->
            <div class="skill-card-vertical">
                <div class="skill-card-img" style="background: linear-gradient(135deg, #e4e7ea 0%, #d1d7dc 100%); position: relative;">
                    <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; height: 200px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                         <div style="width: 80%; height: 80%; background: linear-gradient(45deg, #f8c291, #ff9966); border-radius: 4px;"></div>
                    </div>
                </div>
                <div class="skill-card-footer">
                    <h3>IT Certifications</h3>
                    <i class="fa fa-chevron-right" style="font-size: 12px; color: #1c1d1f;"></i>
                </div>
            </div>

            <!-- Skill 3: Data Science -->
            <div class="skill-card-vertical">
                <div class="skill-card-img" style="background: linear-gradient(135deg, #e4e7ea 0%, #d1d7dc 100%); position: relative;">
                    <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; height: 200px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <div style="width: 80%; height: 80%; background: linear-gradient(45deg, #add8e6, #7f00ff); border-radius: 4px;"></div>
                    </div>
                </div>
                <div class="skill-card-footer">
                    <h3>Data Science</h3>
                    <i class="fa fa-chevron-right" style="font-size: 12px; color: #1c1d1f;"></i>
                </div>
            </div>
        </div>

        <!-- Slider Controls -->
        <div class="slider-controls">
            <button class="slider-btn-mini" style="background: none; border: none; cursor: pointer;"><i class="fa fa-chevron-left" style="color: #6a6f73;"></i></button>
            <div class="dot-nav">
                <div class="dot active"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            <button class="slider-btn-mini" style="background: none; border: none; cursor: pointer;"><i class="fa fa-chevron-right" style="color: #6a6f73;"></i></button>
        </div>
    </section>

    <!-- Google AI Section -->
    <section class="google-ai-section">
        <div class="container">
            <div class="google-ai-header">
                <h2>Learn AI with Google</h2>
            </div>
            <div class="google-ai-container">
                <!-- Main Cert Card -->
                <div class="google-cert-card">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Google_2015_logo.svg/2560px-Google_2015_logo.svg.png" alt="Google" style="height: 20px; width: auto; align-self: flex-start;">
                    <h2>Google AI Professional Certificate</h2>
                    <p>Build your AI fluency and get more done, faster.</p>
                    <div class="cert-meta">
                        <span><i class="fa fa-star" style="color: #b4690e;"></i> 4.8</span>
                        <span>4M ratings</span>
                        <span>4 total hours</span>
                        <span>9 courses</span>
                    </div>
                    <a href="#" class="btn btn-secondary" style="margin-top: auto; border-radius: 4px; padding: 14px;">Learn more</a>
                </div>

                <!-- Course Row -->
                <div class="google-courses-row">
                    <!-- Course 1 -->
                    <div class="google-course-card">
                        <div class="google-course-img" style="background: linear-gradient(45deg, #1fa2ff, #12d8fa);"></div>
                        <div class="google-course-body">
                            <h4>AI Fundamentals</h4>
                            <div class="google-course-meta">
                                Course 1 of 9 &bull; 10 hours
                            </div>
                        </div>
                    </div>
                    <!-- Course 2 -->
                    <div class="google-course-card">
                        <div class="google-course-img" style="background: linear-gradient(45deg, #f093fb, #f5576c);"></div>
                        <div class="google-course-body">
                            <h4>AI for Brainstorming and Planning</h4>
                            <div class="google-course-meta">
                                Course 2 of 9 &bull; 31 mins
                            </div>
                        </div>
                    </div>
                    <!-- Course 3 -->
                    <div class="google-course-card">
                        <div class="google-course-img" style="background: linear-gradient(45deg, #5ee7df, #b490ca);"></div>
                        <div class="google-course-body">
                            <h4>AI for Research and Insights</h4>
                            <div class="google-course-meta">
                                Course 3 of 9 &bull; 31 mins
                            </div>
                        </div>
                    </div>
                    <!-- Course 4 -->
                    <div class="google-course-card">
                        <div class="google-course-img" style="background: linear-gradient(45deg, #f6d365, #fda085);"></div>
                        <div class="google-course-body">
                            <h4>AI for Writing and Communicating</h4>
                            <div class="google-course-meta">
                                Course 4 of 9 &bull; 26 mins
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- AI Era Banner -->
    <section class="container">
        <div class="ai-era-banner">
            <div class="ai-era-content">
                <h2>Reimagine your career in the AI era</h2>
                <p>Future-proof your skills with Personal Plan. Get access to a variety of fresh content from real-world experts.</p>
                <div class="feature-list">
                    <div class="feature-item"><i class="fa fa-sparkles"></i> Learn AI and more</div>
                    <div class="feature-item"><i class="fa fa-certificate"></i> Prep for a certification</div>
                    <div class="feature-item"><i class="fa fa-users"></i> Practice with AI coaching</div>
                    <div class="feature-item"><i class="fa fa-chart-line"></i> Advance your career</div>
                </div>
                <a href="#" class="btn btn-secondary" style="background: white; color: black; border: none; padding: 14px 24px; border-radius: 4px; font-weight: 700;">Learn more</a>
                <p style="margin-top: 15px; font-size: 12px; color: #d1d7dc;">Starting at $10.00/month</p>
            </div>
            <div class="ai-era-visual">
                <div class="visual-box visual-box-lg" style="background: linear-gradient(135deg, #1fa2ff, #12d8fa);"></div>
                <div class="visual-box" style="background: #e4e7ea;">
                    <img src="https://images.squarespace-cdn.com/content/v1/55df6bb1e4b008d758362678/1484196831633-ST2K2RZYW1H3Y9M2VZ00/ke17ZwdGBToddI8pDm48kK6stS_tY3I4D-8Gv9j0o2pZw-zPPgdn4jUwVcJE1ZvWQUxwkmyExglNqGp0IvTJZUJFbgE-7XRK3dMEBRBhUpx_iY5_yidk8F0L_S07I1K5vV-v51vL5v5vU9yM8W_F2a1o1v1v1v1v/ke17ZwdGBToddI8pDm48kK6stS_tY3I4D-8Gv9j0o2pZw-zPPgdn4jUwVcJE1ZvWQUxwkmyExglNqGp0IvTJZUJFbgE-7XRK3dMEBRBhUpx_iY5_yidk8F0L_S07I1K5vV-v51vL5v5vU9yM8W_F2a1o1v1v1v1v" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="visual-box" style="background: #d1d7dc; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-vr-cardboard" style="font-size: 48px; color: #1c1d1f;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills to transform your career -->
    <section class="skills-nav-section container">
        <h2 class="section-title">Skills to transform your career and life</h2>
        <p style="color: var(--gray-color); margin-bottom: 30px;">From critical skills to technical topics, SkillEdu supports your professional development.</p>

        <div class="tabs-container">
            <ul class="tabs-list">
                <li class="tab-item active" data-category="AI">Artificial Intelligence (AI)</li>
                <li class="tab-item" data-category="Python">Python</li>
                <li class="tab-item" data-category="Excel">Microsoft Excel</li>
                <li class="tab-item" data-category="Agents">AI Agents & Agentic AI</li>
                <li class="tab-item" data-category="Marketing">Digital Marketing</li>
                <li class="tab-item" data-category="AWS">Amazon AWS</li>
            </ul>
        </div>

        <div class="course-grid-row">
            <!-- Course 1 -->
            <div class="course-card-v2">
                <div class="thumb" style="background: linear-gradient(45deg, #00d2ff, #3a7bd5);"></div>
                <h4>The AI Engineer Course 2026: Complete AI Engineer Bootcamp</h4>
                <div class="instructor">John Carlson</div>
                <div class="rating" style="display: flex; align-items: center; gap: 4px; font-weight: 700; font-size: 14px; color: #b4690e;">
                    4.8 <span style="font-weight: 400; color: #6a6f73; font-size: 12px;">(15,078 ratings)</span>
                </div>
                <div class="price-row">
                    $9.99 <span class="price-old">$69.99</span>
                </div>
                <div><span class="badge badge-bestseller">Bestseller</span></div>
            </div>

            <!-- Course 2 -->
            <div class="course-card-v2">
                <div class="thumb" style="background: linear-gradient(45deg, #f85032, #f16232);"></div>
                <h4>AI for Leaders: Master Gen AI & No-Code Solutions</h4>
                <div class="instructor">Premium Technologies Private Limited</div>
                <div class="rating" style="display: flex; align-items: center; gap: 4px; font-weight: 700; font-size: 14px; color: #b4690e;">
                    4.7 <span style="font-weight: 400; color: #6a6f73; font-size: 12px;">(18 ratings)</span>
                </div>
                <div class="price-row">
                    $9.99 <span class="price-old">$34.99</span>
                </div>
                <div><span class="badge badge-highest-rated">Highest Rated</span></div>
            </div>

            <!-- Course 3 -->
            <div class="course-card-v2">
                <div class="thumb" style="background: linear-gradient(45deg, #11998e, #38ef7d);"></div>
                <h4>The Complete Guide to AI Infrastructure: Zero to Hero</h4>
                <div class="instructor">Bernard AI</div>
                <div class="rating" style="display: flex; align-items: center; gap: 4px; font-weight: 700; font-size: 14px; color: #b4690e;">
                    4.8 <span style="font-weight: 400; color: #6a6f73; font-size: 12px;">(126 ratings)</span>
                </div>
                <div class="price-row">
                    $9.99 <span class="price-old">$19.99</span>
                </div>
            </div>

            <!-- Course 4 -->
            <div class="course-card-v2">
                <div class="thumb" style="background: linear-gradient(45deg, #8e2de2, #4a00e0);"></div>
                <h4>Certified Chief AI Officer Program: AI Strategy & Governance</h4>
                <div class="instructor">School of AI</div>
                <div class="rating" style="display: flex; align-items: center; gap: 4px; font-weight: 700; font-size: 14px; color: #b4690e;">
                    4.6 <span style="font-weight: 400; color: #6a6f73; font-size: 12px;">(305 ratings)</span>
                </div>
                <div class="price-row">
                    $9.99 <span class="price-old">$19.99</span>
                </div>
                <div><span class="badge badge-bestseller">Bestseller</span></div>
            </div>
        </div>

        <a href="#" style="display: inline-block; margin-top: 20px; color: var(--primary-color); font-weight: 700; text-decoration: none;">Show all Artificial Intelligence (AI) courses <i class="fa fa-chevron-right" style="font-size: 10px;"></i></a>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
