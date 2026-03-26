<?php
require_once 'config/db.php';
session_start();
$base_url = "";
include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/business.css">

<main class="business-page">
    <!-- Hero Section -->
    <section class="business-hero">
        <div class="container hero-flex">
            <div class="hero-text">
                <h1>The learning platform for AI skills and business performance</h1>
                <p>Equip every team to adapt, innovate, and deliver results with SkillEdu Business. Get access to top-rated courses and custom learning paths.</p>
                <div class="hero-actions">
                    <a href="signup.php?role=instructor" class="btn btn-gradient">Get started</a>
                    <a href="#" class="btn btn-secondary">Compare plans</a>
                </div>
                <div class="video-cta">
                    <div class="play-btn">
                        <i class="fa fa-play"></i>
                    </div>
                    <span>Meet Altus: Agentic-powered upskilling. <a href="#">Interested in the Beta?</a></span>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=800" alt="Team Collaboration">
            </div>
        </div>
    </section>

    <!-- Modern Approach Section -->
    <section class="approach-section">
        <div class="container">
            <div class="section-header">
                <h2>Modern skills need a modern learning approach</h2>
                <p>Learning solutions shouldn't be one-size-fits-all. For effective training, you need the right skills and the right modalities. That's where we come in.</p>
            </div>
            <div class="approach-grid">
                <div class="approach-card">
                    <div class="card-icon"><i class="fa fa-tv"></i></div>
                    <h3>On-Demand Learning</h3>
                    <p>Provide anytime access to the latest business, tech, and creative skills from industry-leading instructors.</p>
                    <a href="#" class="learn-more">Learn more <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="approach-card">
                    <div class="card-icon"><i class="fa fa-laptop-code"></i></div>
                    <h3>Hands-On Learning</h3>
                    <p>Boost tech skills faster with SkillEdu Business Pro learn-by-doing labs, workspaces, and assessments.</p>
                    <a href="#" class="learn-more">Learn more <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="approach-card">
                    <div class="card-icon"><i class="fa fa-users"></i></div>
                    <h3>Cohort Learning</h3>
                    <p>Grow your leaders with SkillEdu Business Leadership Academy, an immersive cohort-based experience.</p>
                    <a href="#" class="learn-more">Learn more <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="approach-card">
                    <div class="card-icon"><i class="fa fa-user-tie"></i></div>
                    <h3>Professional Services</h3>
                    <p>Get the expertise and support you need to achieve your goals with our strategic consulting services.</p>
                    <a href="#" class="learn-more">Learn more <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- AI Fluency Section -->
    <section class="ai-fluency-section">
        <div class="container ai-flex">
            <div class="ai-image">
                <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=800" alt="AI Transformation">
            </div>
            <div class="ai-content">
                <span class="badge">New</span>
                <h2>Scale AI fluency across your organization</h2>
                <p>Our new AI Packages help employees at all levels understand, communicate about, and implement AI solutions with confidence and ethical awareness.</p>
                <a href="#" class="btn btn-primary" style="background: black;">Contact us</a>
            </div>
        </div>
    </section>

    <!-- Success Stats Section -->
    <section class="stats-section">
        <div class="container stats-flex">
            <div class="stats-text">
                <h2>Driving profitability through faster staffing and cost optimization</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="number">66%</span>
                        <p>faster new talent onboarding</p>
                        <a href="#" class="stat-link">Publicis Sapient</a>
                    </div>
                    <div class="stat-item">
                        <span class="number">93%</span>
                        <p>retention rate for learning program graduates</p>
                        <a href="#" class="stat-link">Booz Allen Hamilton</a>
                    </div>
                </div>
                <a href="#" class="btn btn-primary" style="background: black; margin-top: 30px;">Explore professional services solutions</a>
            </div>
            <div class="stats-image">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&q=80&w=800" alt="Team Success">
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
