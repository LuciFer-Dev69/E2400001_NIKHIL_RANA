<?php
session_start();
$page_title = "AI Fundamentals";
include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/course_premium.css">

<main class="premium-page-wrapper">
    <!-- Dark Hero Section -->
    <section class="premium-hero">
        <div class="premium-container">
            <div class="hero-main">
                <nav class="breadcrumb-nav">
                    Personal Development <i class="fa fa-chevron-right"></i>
                    Personal Productivity <i class="fa fa-chevron-right"></i>
                    <span>Generative AI (GenAI)</span>
                </nav>

                <div style="margin-bottom: 24px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg" alt="Google" style="height: 32px; filter: brightness(0) invert(1);">
                </div>

                <h1 class="premium-title">AI Fundamentals</h1>

                <div class="course-badge-premium">
                    <i class="fa fa-sparkles"></i> Course 1/7 of <a href="#" style="color: inherit; text-decoration: underline;">Google AI Professional Certificate</a>
                </div>

                <p class="premium-subtitle">Learn the fundamentals of working with AI and how to prompt responsibly so you can leverage it effectively at work.</p>

                <div class="premium-meta">
                    <div class="premium-meta-item">
                        <img src="https://www.gstatic.com/images/branding/product/1x/googleg_32dp.png" alt="G" style="height: 16px; margin-right: 8px;">
                        Created by <a href="#" style="color: #fff; text-decoration: underline; margin-left: 5px;">Google</a>
                    </div>
                </div>

                <div class="premium-meta" style="margin-top: 15px; opacity: 0.9;">
                    <div class="premium-meta-item"><i class="fa fa-exclamation-circle"></i> Last updated 3/2026</div>
                    <div class="premium-meta-item"><i class="fa fa-globe"></i> English</div>
                    <div class="premium-meta-item"><i class="fa fa-closed-captioning"></i> Bulgarian [Auto], Czech [Auto], <a href="#" style="color: #fff;">28 more</a></div>
                </div>

                <!-- Premium Info Box -->
                <div class="premium-info-grid">
                    <div class="premium-tab">
                        <i class="fa fa-check-circle"></i>
                        <span>Premium</span>
                    </div>
                    <div class="premium-info-content">
                        <div class="premium-info-text">
                            Access 26,000+ top-rated courses with SkillEdu Personal Plan.
                        </div>
                        <div class="premium-stats">
                            <div class="stat-item">
                                <span class="stat-value">4.5</span>
                                <div class="stars" style="justify-content: center; margin-bottom: 4px;">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
                                </div>
                                <a href="#reviews" class="stat-label" style="text-decoration: underline;">290 ratings</a>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value">15,200</span>
                                <span class="stat-label">learners</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Sidebar -->
            <div class="premium-sidebar-wrapper">
                <aside class="premium-sidebar">
                    <div class="sidebar-preview" onclick="openVideoModal()">
                        <img src="https://images.unsplash.com/photo-1573163281530-5be9c2d1872b?auto=format&fit=crop&q=80&w=600" alt="Preview">
                        <div class="play-overlay">
                            <div style="position: absolute; top: 20px; right: 20px; background: #fff; padding: 4px; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                <img src="https://www.gstatic.com/images/branding/product/1x/googleg_32dp.png" alt="G" style="height: 24px;">
                            </div>
                            <div style="position: absolute; top: 100px; left: 0; right: 0; text-align: center; color: rgba(255,255,255,0.7); font-size: 13px; font-weight: 700;">Created by Google</div>
                            <i class="fa fa-play-circle"></i>
                            <span style="font-weight: 700; font-size: 15px;">Preview this course</span>
                        </div>
                    </div>
                    <div class="sidebar-content">
                        <div class="plan-info">
                            <i class="fa fa-info-circle" style="font-size: 20px;"></i>
                            <p style="margin: 0; font-weight: 700;">Part of the 'Learn AI with Google' plan</p>
                            <p style="margin: 5px 0 0; color: var(--text-muted); font-size: 13px;">This course can only be purchased as part of a subscription offering. <a href="#" style="color: var(--udemy-accent); text-decoration: underline;">Learn more</a></p>
                        </div>

                        <div class="sidebar-price">
                            From $12.00 <span>/month</span>
                        </div>

                        <ul class="sidebar-features">
                            <li><i class="fa fa-road"></i> 3 career learning paths</li>
                            <li><i class="fa fa-tasks"></i> 20+ activities for real-world practice</li>
                            <li><i class="fa fa-certificate"></i> Industry-recognized credentials from Google</li>
                            <li><i class="fa fa-sparkles"></i> Access to Google AI Pro for 3 months at no cost <a href="#" style="font-size: 11px;">(terms apply)</a></li>
                        </ul>

                        <button class="btn-get-started">Get started <i class="fa fa-arrow-right" style="margin-left: 8px;"></i></button>
                        <p style="text-align: center; font-size: 12px; color: var(--text-muted); margin-top: 15px;">Billed monthly or annually. Cancel anytime.</p>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <section class="premium-container content-section">
        <!-- Mobile Sidebar Placeholder -->
        <div id="mobile-sidebar-placeholder" class="mobile-sidebar-container"></div>

        <div class="content-main-wrapper">
            <!-- Achievement Section -->
            <div class="certificate-section">
                <img src="https://www.gstatic.com/images/branding/product/1x/googleg_32dp.png" alt="Google" class="cert-logo">
                <div class="cert-content">
                    <h3>Earn a certificate from Google</h3>
                    <p>After completion of all courses in the <a href="#" style="color: var(--udemy-accent); text-decoration: underline;">Google AI Professional Certificate</a> path, earn a professional certificate that you can share on social media, LinkedIn, resume, or CV.</p>
                </div>
                <i class="fa fa-arrow-right cert-arrow"></i>
            </div>

            <!-- What you'll learn -->
            <div class="learning-box">
                <h2>What you'll learn</h2>
                <ul class="learning-list">
                    <li><i class="fa fa-check"></i> Understand fundamental generative AI concepts and build a foundation for professional AI use.</li>
                    <li><i class="fa fa-check"></i> Construct effective prompts using a structured framework to generate high-quality, relevant outputs for workplace tasks.</li>
                    <li><i class="fa fa-check"></i> Evaluate AI-generated outputs for accuracy and bias.</li>
                    <li><i class="fa fa-check"></i> Analyze your professional workflow to identify high-impact AI opportunities.</li>
                </ul>
            </div>

            <!-- Explore related topics -->
            <div style="margin-bottom: 48px;">
                <h2 style="font-size: 24px; margin-bottom: 16px;">Explore related topics</h2>
                <div style="display: flex; flex-wrap: wrap;">
                    <a href="#" class="topic-tag">Generative AI (GenAI)</a>
                    <a href="#" class="topic-tag">Artificial Intelligence (AI)</a>
                    <a href="#" class="topic-tag">Machine Learning</a>
                    <a href="#" class="topic-tag">Google Gemini (Bard)</a>
                    <a href="#" class="topic-tag">Personal Productivity</a>
                </div>
            </div>

            <!-- This course includes -->
            <div style="margin-bottom: 48px;">
                <h2 style="font-size: 24px; margin-bottom: 16px;">This course includes:</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px; font-size: 14px;"><i class="fa fa-video" style="width: 20px;"></i> 35 mins on-demand video</div>
                    <div style="display: flex; align-items: center; gap: 12px; font-size: 14px;"><i class="fa fa-mobile-alt" style="width: 20px;"></i> Access on mobile and TV</div>
                    <div style="display: flex; align-items: center; gap: 12px; font-size: 14px;"><i class="fa fa-file-invoice" style="width: 20px;"></i> 1 practice test</div>
                    <div style="display: flex; align-items: center; gap: 12px; font-size: 14px;"><i class="fa fa-closed-captioning" style="width: 20px;"></i> Closed captions</div>
                    <div style="display: flex; align-items: center; gap: 12px; font-size: 14px;"><i class="fa fa-clipboard-list" style="width: 20px;"></i> Assignments</div>
                    <div style="display: flex; align-items: center; gap: 12px; font-size: 14px;"><i class="fa fa-award" style="width: 20px;"></i> Certificate of completion</div>
                    <div style="display: flex; align-items: center; gap: 12px; font-size: 14px;"><i class="fa fa-file-alt" style="width: 20px;"></i> 15 articles</div>
                </div>
            </div>

            <!-- Course content -->
            <div style="margin-bottom: 48px;">
                <h2 style="font-size: 24px; margin-bottom: 16px;">Course content</h2>
                <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 10px;">
                    <span>6 sections &bull; 27 lectures &bull; 1h 13m total length</span>
                    <a href="#" style="color: var(--udemy-accent); font-weight: 700;">Expand all sections</a>
                </div>

                <div class="accordion-container">
                    <div class="accordion-item">
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <div><i class="fa fa-chevron-down" style="margin-right: 12px;"></i> Collaborate with AI</div>
                            <span style="font-weight: 400; font-size: 14px;">4 lectures &bull; 8min</span>
                        </div>
                        <div class="accordion-body" style="display: block;">
                            <div class="lecture-item">
                                <span><i class="fa fa-play-circle" style="margin-right: 12px;"></i> AI fundamentals</span>
                                <div><a href="#" style="color: var(--udemy-accent); text-decoration: underline; margin-right: 15px;">Preview</a> 3:04</div>
                            </div>
                            <div class="lecture-item">
                                <span><i class="fa fa-play-circle" style="margin-right: 12px;"></i> Adopt a collaborative mindset with AI</span>
                                <div><a href="#" style="color: var(--udemy-accent); text-decoration: underline; margin-right: 15px;">Preview</a> 1:30</div>
                            </div>
                            <div class="lecture-item">
                                <span><i class="fa fa-play-circle" style="margin-right: 12px;"></i> Three ways to collaborate with AI</span>
                                <div>1:40</div>
                            </div>
                            <div class="lecture-item">
                                <span><i class="fa fa-play-circle" style="margin-right: 12px;"></i> Maya, what advice do you have for people getting started with AI?</span>
                                <div><a href="#" style="color: var(--udemy-accent); text-decoration: underline; margin-right: 15px;">Preview</a> 1:00</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <div><i class="fa fa-chevron-right" style="margin-right: 12px;"></i> Practice using AI</div>
                            <span style="font-weight: 400; font-size: 14px;">4 lectures &bull; 9min</span>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <div><i class="fa fa-chevron-right" style="margin-right: 12px;"></i> Learn how AI works</div>
                            <span style="font-weight: 400; font-size: 14px;">4 lectures &bull; 15min</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students also bought -->
            <div style="margin-bottom: 48px;">
                <h2 style="font-size: 24px; margin-bottom: 16px;">Students also bought</h2>
                <div class="also-bought-list">
                    <!-- Item 1 -->
                    <div style="display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid var(--border-soft); align-items: center;">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=100" style="width: 64px; height: 64px; object-fit: cover; border-radius: 4px;">
                        <div style="flex: 1;">
                            <h4 style="font-size: 16px; margin-bottom: 4px;">Full stack generative and Agentic AI with python</h4>
                            <div style="display: flex; gap: 8px; align-items: center; font-size: 13px;">
                                <span style="background: #ecebfe; padding: 2px 6px; border-radius: 2px; font-weight: 700; color: #3c1d9d;">Bestseller</span>
                                <span style="color: #b4690e; font-weight: 700;">4.5 <i class="fa fa-star"></i></span>
                                <span style="color: var(--text-muted);">39,866 students</span>
                                <span style="color: var(--text-muted);">32.5 hours</span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700;">$9.99</div>
                            <div style="color: var(--text-muted); text-decoration: line-through; font-size: 13px;">$19.99</div>
                        </div>
                    </div>
                    <!-- Item 2 -->
                    <div style="display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid var(--border-soft); align-items: center;">
                        <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=100" style="width: 64px; height: 64px; object-fit: cover; border-radius: 4px;">
                        <div style="flex: 1;">
                            <h4 style="font-size: 16px; margin-bottom: 4px;">Succeed in the Age of AI</h4>
                            <div style="display: flex; gap: 8px; align-items: center; font-size: 13px;">
                                <span style="background: #ecebfe; padding: 2px 6px; border-radius: 2px; font-weight: 700; color: #3c1d9d;">Bestseller</span>
                                <span style="color: #b4690e; font-weight: 700;">4.6 <i class="fa fa-star"></i></span>
                                <span style="color: var(--text-muted);">13,725 students</span>
                                <span style="color: var(--text-muted);">6.5 hours</span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700;">$9.99</div>
                            <div style="color: var(--text-muted); text-decoration: line-through; font-size: 13px;">$24.99</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div id="reviews" style="margin-bottom: 60px;">
                <h2 style="font-size: 24px; margin-bottom: 24px;"><i class="fa fa-star" style="color: #b4690e;"></i> 4.5 course rating &bull; 290 ratings</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                    <!-- Review 1 -->
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-avatar">AK</div>
                            <div class="review-info">
                                <h4>Amiya Krishna</h4>
                                <div class="stars"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> <span style="color: var(--text-muted); margin-left:8px;">15 days ago</span></div>
                            </div>
                        </div>
                        <div class="review-body">
                            This course offers an easy-to-understand introduction to AI principles and real-world applications. The examples and straightforward explanations make it simple to see how AI tools can enhance daily tasks and creativity.
                        </div>
                    </div>
                    <!-- Review 2 -->
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-avatar">K</div>
                            <div class="review-info">
                                <h4>Kyle</h4>
                                <div class="stars"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> <span style="color: var(--text-muted); margin-left:8px;">17 days ago</span></div>
                            </div>
                        </div>
                        <div class="review-body">
                            It's a good overview of what AI is and its use cases. I'm enjoying the course. It's also breaking down some of the AI jargon for me :)
                        </div>
                    </div>
                </div>

                <button class="btn-outline-premium">Show all reviews</button>
            </div>
        </div>
    </section>
</main>

<!-- Video Preview Modal -->
<div class="modal-overlay" id="videoModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Course Preview - AI Fundamentals</h3>
            <button class="close-modal" onclick="closeVideoModal()">&times;</button>
        </div>
        <div class="video-player-container">
            <video id="previewPlayer" controls poster="https://images.unsplash.com/photo-1620712943543-bcc4688e7485?auto=format&fit=crop&q=80&w=1200">
                <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="modal-content-scroll">
            <div class="modal-section-title">Free Sample Videos:</div>
            <ul class="preview-video-list">
                <li class="preview-item active" onclick="playPreview('https://www.w3schools.com/html/mov_bbb.mp4', this)">
                    <div class="preview-thumb">
                        <img src="https://images.unsplash.com/photo-1573163281530-5be9c2d1872b?auto=format&fit=crop&q=80&w=100" alt="Thumb">
                    </div>
                    <div class="preview-info">
                        <span class="preview-title-text">AI Fundamentals</span>
                        <span class="preview-duration">1:30</span>
                    </div>
                </li>
                <li class="preview-item" onclick="playPreview('https://www.w3schools.com/html/movie.mp4', this)">
                    <div class="preview-thumb">
                        <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&q=80&w=200" alt="Thumb">
                    </div>
                    <div class="preview-info">
                        <span class="preview-title-text">Adopt a collaborative mindset with AI</span>
                        <span class="preview-duration">3:04</span>
                    </div>
                </li>
                <li class="preview-item" onclick="playPreview('https://www.w3schools.com/html/mov_bbb.mp4', this)">
                    <div class="preview-thumb">
                        <img src="https://images.unsplash.com/photo-1573163281530-5be9c2d1872b?auto=format&fit=crop&q=80&w=100" alt="Thumb">
                    </div>
                    <div class="preview-info">
                        <span class="preview-title-text">Maya, what advice do you have?</span>
                        <span class="preview-duration">1:00</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('videoModal');
    const player = document.getElementById('previewPlayer');

    function openVideoModal() {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        player.play();
    }

    function closeVideoModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        player.pause();
    }

    function playPreview(src, element) {
        player.src = src;
        player.play();
        
        // Update active state
        document.querySelectorAll('.preview-item').forEach(item => item.classList.remove('active'));
        element.classList.add('active');
    }

    // Close on overlay click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeVideoModal();
    });

    // Sidebar Responsiveness Script
    const sidebar = document.querySelector('.premium-sidebar');
    const desktopWrapper = document.querySelector('.premium-sidebar-wrapper');
    const mobilePlaceholder = document.getElementById('mobile-sidebar-placeholder');

    function handleSidebarPosition() {
        if (window.innerWidth <= 991) {
            if (sidebar.parentElement !== mobilePlaceholder) {
                mobilePlaceholder.appendChild(sidebar);
            }
        } else {
            if (sidebar.parentElement !== desktopWrapper) {
                desktopWrapper.appendChild(sidebar);
            }
        }
    }

    window.addEventListener('resize', handleSidebarPosition);
    window.addEventListener('load', handleSidebarPosition);

    function toggleAccordion(header) {
        const body = header.nextElementSibling;
        const icon = header.querySelector('i');
        
        if (body.style.display === 'block') {
            body.style.display = 'none';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-right');
        } else {
            body.style.display = 'block';
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-down');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
