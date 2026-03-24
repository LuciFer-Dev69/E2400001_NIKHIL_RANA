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

        <!-- Promo Hero Slider -->
        <section class="hero container">
            <div style="position: relative; height: 320px; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div class="slider-container" style="display: flex; height: 100%;">
                    <!-- Slide 1 -->
                    <div class="slide" style="background-image: url('assets/images/1.jpg'); background-size: cover; background-position: center; position: relative; padding: 40px; display: flex; align-items: center;">
                        <div class="promo-card" style="background: rgba(255, 255, 255, 0.95); padding: 24px; max-width: 400px; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: relative; z-index: 2;">
                            <h2>Courses from $9.99</h2>
                            <p>Start building your learning routine today. Offer ends March 27.</p>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="slide" style="background-image: url('assets/images/2.jpg'); background-size: cover; background-position: center; position: relative; padding: 40px; display: flex; align-items: center;">
                        <div class="promo-card" style="background: rgba(255, 255, 255, 0.95); padding: 24px; max-width: 400px; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: relative; z-index: 2;">
                            <h2>Level up your skills</h2>
                            <p>Unlock new career opportunities with expert-crafted content.</p>
                        </div>
                    </div>
                </div>
                <button class="slider-btn prev-btn" style="z-index: 10;"><i class="fa fa-chevron-left"></i></button>
                <button class="slider-btn next-btn" style="z-index: 10;"><i class="fa fa-chevron-right"></i></button>
            </div>
        </section>
    <?php
else: ?>
        <!-- Hero Section (Guest) -->
        <section class="hero container">
            <div style="position: relative; height: 480px; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);">
                <div class="slider-container" style="display: flex; height: 100%;">
                    <!-- Slide 1 -->
                    <div class="slide" style="background-image: url('assets/images/1.jpg'); background-size: cover; background-position: center; position: relative;">
                        <!-- Gradient overlay to keep text readable against varied images -->
                        <div style="position: absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(90deg, rgba(0,0,0,0.6) 0%, transparent 60%); z-index: 1;"></div>
                        <div class="hero-content" style="z-index: 2; position: relative;">
                            <h1>Courses from $9.99</h1>
                            <p>Start building your learning routine today. Offer ends March 27.</p>
                            <a href="#" class="btn btn-primary" style="background: var(--primary-color); color: #fff; border: none; padding: 12px 28px; border-radius: 4px; text-decoration: none; display: inline-block; font-weight: 600; transition: 0.3s;">Explore Courses</a>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="slide" style="background-image: url('assets/images/2.jpg'); background-size: cover; background-position: center; position: relative;">
                        <div style="position: absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(90deg, rgba(0,0,0,0.6) 0%, transparent 60%); z-index: 1;"></div>
                        <div class="hero-content" style="z-index: 2; position: relative;">
                            <h1>Learn from the Best</h1>
                            <p>Discover thousands of courses updated daily by top instructors.</p>
                            <a href="#" class="btn btn-primary" style="background: var(--primary-color); color: #fff; border: none; padding: 12px 28px; border-radius: 4px; text-decoration: none; display: inline-block; font-weight: 600; transition: 0.3s;">View Categories</a>
                        </div>
                    </div>
                </div>
                <button class="slider-btn prev-btn" style="z-index: 10;"><i class="fa fa-chevron-left"></i></button>
                <button class="slider-btn next-btn" style="z-index: 10;"><i class="fa fa-chevron-right"></i></button>
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

        <div class="skills-grid" id="skillsGrid" style="display: flex; gap: 20px; overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; scroll-snap-type: x mandatory; padding-bottom: 20px;">
            <style>#skillsGrid::-webkit-scrollbar { display: none; }</style>
            <!-- Skill 1: Generative AI -->
            <div class="skill-card-vertical" style="flex: 0 0 calc(33.333% - 14px); scroll-snap-align: start;">
                <div class="skill-card-img" style="background: linear-gradient(135deg, #e4e7ea 0%, #d1d7dc 100%); position: relative;">
                    <!-- Placeholder for image -->
                    <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; height: 200px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <div style="width: 80%; height: 80%; background: url('https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&q=80&w=400') center/cover no-repeat; border-radius: 4px;"></div>
                    </div>
                </div>
                <div class="skill-card-footer">
                    <h3>Generative AI</h3>
                    <i class="fa fa-chevron-right" style="font-size: 12px; color: #1c1d1f;"></i>
                </div>
            </div>

            <!-- Skill 2: IT Certifications -->
            <div class="skill-card-vertical" style="flex: 0 0 calc(33.333% - 14px); scroll-snap-align: start;">
                <div class="skill-card-img" style="background: linear-gradient(135deg, #e4e7ea 0%, #d1d7dc 100%); position: relative;">
                    <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; height: 200px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                         <div style="width: 80%; height: 80%; background: url('https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=400') center/cover no-repeat; border-radius: 4px;"></div>
                    </div>
                </div>
                <div class="skill-card-footer">
                    <h3>IT Certifications</h3>
                    <i class="fa fa-chevron-right" style="font-size: 12px; color: #1c1d1f;"></i>
                </div>
            </div>

            <!-- Skill 3: Data Science -->
            <div class="skill-card-vertical" style="flex: 0 0 calc(33.333% - 14px); scroll-snap-align: start;">
                <div class="skill-card-img" style="background: linear-gradient(135deg, #e4e7ea 0%, #d1d7dc 100%); position: relative;">
                    <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; height: 200px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <div style="width: 80%; height: 80%; background: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=400') center/cover no-repeat; border-radius: 4px;"></div>
                    </div>
                </div>
                <div class="skill-card-footer">
                    <h3>Data Science</h3>
                    <i class="fa fa-chevron-right" style="font-size: 12px; color: #1c1d1f;"></i>
                </div>
            </div>

            <!-- Skill 4: Web Development -->
            <div class="skill-card-vertical" style="flex: 0 0 calc(33.333% - 14px); scroll-snap-align: start;">
                <div class="skill-card-img" style="background: linear-gradient(135deg, #e4e7ea 0%, #d1d7dc 100%); position: relative;">
                    <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; height: 200px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <div style="width: 80%; height: 80%; background: url('https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&q=80&w=400') center/cover no-repeat; border-radius: 4px;"></div>
                    </div>
                </div>
                <div class="skill-card-footer">
                    <h3>Web Development</h3>
                    <i class="fa fa-chevron-right" style="font-size: 12px; color: #1c1d1f;"></i>
                </div>
            </div>

            <!-- Skill 5: Digital Marketing -->
            <div class="skill-card-vertical" style="flex: 0 0 calc(33.333% - 14px); scroll-snap-align: start;">
                <div class="skill-card-img" style="background: linear-gradient(135deg, #e4e7ea 0%, #d1d7dc 100%); position: relative;">
                    <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; height: 200px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <div style="width: 80%; height: 80%; background: url('https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?auto=format&fit=crop&q=80&w=400') center/cover no-repeat; border-radius: 4px;"></div>
                    </div>
                </div>
                <div class="skill-card-footer">
                    <h3>Digital Marketing</h3>
                    <i class="fa fa-chevron-right" style="font-size: 12px; color: #1c1d1f;"></i>
                </div>
            </div>
        </div>

        <!-- Slider Controls -->
        <div class="slider-controls">
            <button class="slider-btn-mini prev-skill" style="background: none; border: none; cursor: pointer;"><i class="fa fa-chevron-left" style="color: #6a6f73;"></i></button>
            <div class="dot-nav skills-dots">
                <div class="dot active"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            <button class="slider-btn-mini next-skill" style="background: none; border: none; cursor: pointer;"><i class="fa fa-chevron-right" style="color: #6a6f73;"></i></button>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const grid = document.getElementById('skillsGrid');
                const prev = document.querySelector('.prev-skill');
                const next = document.querySelector('.next-skill');
                const dots = document.querySelectorAll('.skills-dots .dot');
                
                function updateDots() {
                    if (!grid || dots.length === 0) return;
                    const maxScroll = Math.max(1, grid.scrollWidth - grid.clientWidth);
                    const scrollPercentage = grid.scrollLeft / maxScroll;
                    
                    dots.forEach(dot => dot.classList.remove('active'));
                    
                    if (scrollPercentage < 0.33) {
                        dots[0].classList.add('active');
                    } else if (scrollPercentage < 0.66) {
                        dots[1].classList.add('active');
                    } else {
                        dots[2].classList.add('active');
                    }
                }
                
                if (grid) {
                    grid.addEventListener('scroll', updateDots);
                    window.addEventListener('resize', updateDots);
                    
                    if (next) {
                        next.addEventListener('click', () => {
                            grid.scrollBy({ left: grid.clientWidth * 0.5, behavior: 'smooth' });
                        });
                    }
                    
                    if (prev) {
                        prev.addEventListener('click', () => {
                            grid.scrollBy({ left: -grid.clientWidth * 0.5, behavior: 'smooth' });
                        });
                    }
                    
                    dots.forEach((dot, index) => {
                        dot.addEventListener('click', () => {
                            const maxScroll = Math.max(0, grid.scrollWidth - grid.clientWidth);
                            const targetScroll = maxScroll * (index / (dots.length - 1));
                            grid.scrollTo({ left: targetScroll, behavior: 'smooth' });
                        });
                    });
                }
            });
        </script>
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
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg" alt="Google" style="height: 20px; width: auto; align-self: flex-start;">
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
                        <div class="google-course-img" style="background: url('https://images.unsplash.com/photo-1620712943543-bcc4688e7485?auto=format&fit=crop&q=80&w=400') center/cover no-repeat;"></div>
                        <div class="google-course-body">
                            <h4>AI Fundamentals</h4>
                            <div class="google-course-meta">
                                Course 1 of 9 &bull; 10 hours
                            </div>
                        </div>
                    </div>
                    <!-- Course 2 -->
                    <div class="google-course-card">
                        <div class="google-course-img" style="background: url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=400') center/cover no-repeat;"></div>
                        <div class="google-course-body">
                            <h4>AI for Brainstorming and Planning</h4>
                            <div class="google-course-meta">
                                Course 2 of 9 &bull; 31 mins
                            </div>
                        </div>
                    </div>
                    <!-- Course 3 -->
                    <div class="google-course-card">
                        <div class="google-course-img" style="background: url('https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&q=80&w=400') center/cover no-repeat;"></div>
                        <div class="google-course-body">
                            <h4>AI for Research and Insights</h4>
                            <div class="google-course-meta">
                                Course 3 of 9 &bull; 31 mins
                            </div>
                        </div>
                    </div>
                    <!-- Course 4 -->
                    <div class="google-course-card">
                        <div class="google-course-img" style="background: url('https://images.unsplash.com/photo-1499591934245-40b55745b905?auto=format&fit=crop&q=80&w=400') center/cover no-repeat;"></div>
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
                <div class="visual-box visual-box-lg" style="background: url('https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&q=80&w=600') center/cover no-repeat;"></div>
                <div class="visual-box" style="background: #e4e7ea;">
                    <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=400" style="width: 100%; height: 100%; object-fit: cover;">
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

        <?php include 'includes/home_tabs.php'; ?>
    </section>

    <!-- Top Categories Section -->
    <section class="top-categories-section container" style="margin-top: 60px; margin-bottom: 60px;">
        <h2 class="section-title">Top Categories</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-top: 30px;">
            <!-- Category 1 -->
            <a href="<?php echo $base_url; ?>courses.php?category=development" style="text-decoration: none; color: inherit; display: block; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';">
                <div style="height: 160px; background: url('https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&q=80&w=400') center/cover no-repeat;"></div>
                <div style="padding: 16px; background: #fff; font-weight: 700; font-size: 16px;">Development</div>
            </a>
            <!-- Category 2 -->
            <a href="<?php echo $base_url; ?>courses.php?category=business" style="text-decoration: none; color: inherit; display: block; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';">
                <div style="height: 160px; background: url('https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&q=80&w=400') center/cover no-repeat;"></div>
                <div style="padding: 16px; background: #fff; font-weight: 700; font-size: 16px;">Business</div>
            </a>
            <!-- Category 3 -->
            <a href="<?php echo $base_url; ?>courses.php?category=design" style="text-decoration: none; color: inherit; display: block; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';">
                <div style="height: 160px; background: url('https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&q=80&w=400') center/cover no-repeat;"></div>
                <div style="padding: 16px; background: #fff; font-weight: 700; font-size: 16px;">Design</div>
            </a>
            <!-- Category 4 -->
            <a href="<?php echo $base_url; ?>courses.php?category=it-software" style="text-decoration: none; color: inherit; display: block; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';">
                <div style="height: 160px; background: url('https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&q=80&w=400') center/cover no-repeat;"></div>
                <div style="padding: 16px; background: #fff; font-weight: 700; font-size: 16px;">IT & Software</div>
            </a>
        </div>
        </div>
    </section>

    <!-- Hero Slider JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flexContainer = document.querySelector('.hero .slider-container');
            const slides = document.querySelectorAll('.hero .slide');
            const prevBtn = document.querySelector('.hero .prev-btn');
            const nextBtn = document.querySelector('.hero .next-btn');

            if (flexContainer && slides.length > 0) {
                let currentSlide = 0;
                const totalSlides = slides.length;
                
                // Set flex container to allow sliding
                flexContainer.style.display = 'flex';
                flexContainer.style.transition = 'transform 0.5s ease-in-out';
                flexContainer.style.width = (totalSlides * 100) + '%';
                
                slides.forEach(slide => {
                    slide.style.width = (100 / totalSlides) + '%';
                    slide.style.minWidth = (100 / totalSlides) + '%';
                    slide.style.flexShrink = '0';
                });
                
                function goToSlide(index) {
                    currentSlide = index;
                    flexContainer.style.transform = `translateX(-${currentSlide * (100 / totalSlides)}%)`;
                }
                
                function next() {
                    currentSlide = (currentSlide + 1) % totalSlides;
                    goToSlide(currentSlide);
                }
                
                function prev() {
                    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                    goToSlide(currentSlide);
                }
                
                let autoTimer = setInterval(next, 2000);
                
                function resetTimer() {
                    clearInterval(autoTimer);
                    autoTimer = setInterval(next, 2000);
                }
                
                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        next();
                        resetTimer();
                    });
                }
                
                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        prev();
                        resetTimer();
                    });
                }
            }
        });
    </script>
</main>

<?php include 'includes/footer.php'; ?>
