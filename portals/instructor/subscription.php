<?php
/**
 * portals/instructor/subscription.php
 * 
 * Advanced Instructor Subscription Dashboard
 * Featuring 3D tilt effects, interactive cards, and high-fidelity modals.
 */
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'instructor') {
    header('Location: ../../login.php');
    exit();
}

$page_title = 'Subscription Plans | SkillEdu Instructor';
include '../../includes/instructor/instructor_header.php';
?>

<link rel="stylesheet" href="../../assets/css/instructor_subscription.css">

<div class="subscription-page">
    <div class="pricing-header">
        <h1>Scale Your Teaching Business</h1>
        <p style="color: var(--gray-color); font-size: 18px;">Unlock the full potential of SkillEdu with our advanced creator tools.</p>
    </div>

    <!-- Toggle Section -->
    <div class="toggle-wrapper" style="margin-bottom: 50px;">
        <span style="font-weight: 700; color: var(--dark-color);">Monthly</span>
        <label class="switch">
            <input type="checkbox" id="billingToggle">
            <span class="slider"></span>
        </label>
        <span style="font-weight: 700; color: var(--dark-color);">Yearly <span style="background: rgba(46, 204, 113, 0.1); color: #2ecc71; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-left: 5px;">Save 20%</span></span>
    </div>

    <!-- Interactive Pricing Grid -->
    <div class="pricing-grid">
        <!-- Free Plan -->
        <div class="pricing-card">
            <div class="plan-name">Free Starter</div>
            <div class="plan-price">$0 <span>/forever</span></div>
            <p style="color: var(--gray-color); font-size: 14px; margin-bottom: 25px;">Perfect for individual creators starting out.</p>
            <div class="feature-chips">
                <span class="feature-chip">1 Course</span>
                <span class="feature-chip">10% Fee</span>
            </div>
            <ul class="feature-list">
                <li><i class="fa fa-circle-check"></i> 1 Published Course</li>
                <li><i class="fa fa-circle-check"></i> Basic Analytics</li>
                <li><i class="fa fa-circle-check"></i> Standard Quality</li>
            </ul>
            <button class="btn-select-plan btn btn-secondary" style="border: 1px solid var(--border-color); color: var(--dark-color); opacity: 0.7; cursor: default;">Current Plan</button>
        </div>

        <!-- Pro Plan -->
        <div class="pricing-card featured">
            <div class="popular-badge">POWER USER</div>
            <div class="plan-name">Instructor Pro</div>
            <div class="plan-price" id="pro-price" data-plan="pro">$29 <span>/mo</span></div>
            <p style="color: var(--gray-color); font-size: 14px; margin-bottom: 10px;" class="billing-period-label">Billed monthly</p>
            <div class="feature-chips" style="margin-bottom: 15px;">
                <span class="feature-chip" style="background: rgba(255, 65, 108, 0.1); color: #FF416C;">10 Courses</span>
                <span class="feature-chip" style="background: rgba(255, 65, 108, 0.1); color: #FF416C;">5% Fee</span>
            </div>
            <ul class="feature-list">
                <li><i class="fa fa-circle-check"></i> 10 Published Courses</li>
                <li><i class="fa fa-circle-check"></i> Advanced Analytics</li>
                <li><i class="fa fa-circle-check"></i> Priority Email Support</li>
                <li><i class="fa fa-circle-check"></i> Custom Certificates</li>
            </ul>
            <button class="btn-select-plan btn-primary" style="background: var(--primary-gradient); border: none;" onclick="selectInstructorPlan('Instructor Pro')">Go Pro Now</button>
            <a href="javascript:void(0)" onclick="openComparisonModal()" style="display: block; margin-top: 15px; font-size: 13px; color: var(--primary-color); font-weight: 700; text-decoration: none;">View Detail Comparison</a>
        </div>

        <!-- Enterprise Plan -->
        <div class="pricing-card">
            <div class="plan-name">School Hub</div>
            <div class="plan-price" id="ent-price" data-plan="enterprise">$99 <span>/mo</span></div>
            <p style="color: var(--gray-color); font-size: 14px; margin-bottom: 10px;" class="billing-period-label">Billed monthly</p>
            <div class="feature-chips" style="margin-bottom: 15px;">
                <span class="feature-chip">Unlimited</span>
                <span class="feature-chip">2% Fee</span>
            </div>
            <ul class="feature-list">
                <li><i class="fa fa-circle-check"></i> Unlimited Courses</li>
                <li><i class="fa fa-circle-check"></i> Real-time Revenue API</li>
                <li><i class="fa fa-circle-check"></i> White-label Player</li>
                <li><i class="fa fa-circle-check"></i> Dedicated Manager</li>
            </ul>
            <button class="btn-select-plan btn btn-secondary" style="border: 1px solid var(--border-color); color: var(--dark-color);" onclick="selectInstructorPlan('Enterprise')">Contact for School</button>
        </div>
    </div>
</div>

<!-- Comparison Modal -->
<div class="modal-overlay" id="comparisonModal" onclick="if(event.target === this) closeComparisonModal()">
    <div class="modal-content">
        <button class="close-modal" onclick="closeComparisonModal()"><i class="fa fa-times"></i></button>
        <h2 style="font-weight: 800; font-size: 28px; margin-bottom: 30px;">Plan Comparison</h2>
        
        <table class="comparison-table">
            <thead>
                <tr>
                    <th>Feature Capability</th>
                    <th style="color: var(--gray-color);">Starter</th>
                    <th style="color: var(--primary-color);">Pro</th>
                    <th style="color: var(--dark-color);">Enterprise</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Commission Fee</td>
                    <td>10%</td>
                    <td>5%</td>
                    <td>2%</td>
                </tr>
                <tr>
                    <td>Course Publication Limit</td>
                    <td>1 Course</td>
                    <td>10 Courses</td>
                    <td>Unlimited</td>
                </tr>
                <tr>
                    <td>Storage Limit</td>
                    <td>10 GB</td>
                    <td>100 GB</td>
                    <td>Unlimited</td>
                </tr>
                <tr>
                    <td>Custom Branding</td>
                    <td><i class="fa fa-times" style="color: #e74c3c;"></i></td>
                    <td><i class="fa fa-check" style="color: #2ecc71;"></i></td>
                    <td><i class="fa fa-check" style="color: #2ecc71;"></i></td>
                </tr>
                <tr>
                    <td>Bulk Student Enrollment</td>
                    <td><i class="fa fa-times" style="color: #e74c3c;"></i></td>
                    <td><i class="fa fa-times" style="color: #e74c3c;"></i></td>
                    <td><i class="fa fa-check" style="color: #2ecc71;"></i></td>
                </tr>
                <tr>
                    <td>Instant Payouts</td>
                    <td><i class="fa fa-times" style="color: #e74c3c;"></i></td>
                    <td><i class="fa fa-check" style="color: #2ecc71;"></i></td>
                    <td><i class="fa fa-check" style="color: #2ecc71;"></i></td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 40px; text-align: center; background: var(--light-gray); padding: 25px; border-radius: 12px;">
            <p style="font-size: 14px; color: var(--gray-color); margin-bottom: 20px;">Still have questions about which plan fits your needs?</p>
            <a href="mailto:support@skilledu.com" class="btn btn-primary" style="padding: 12px 30px;">Talk to our Team</a>
        </div>
    </div>
</div>

<script src="../../assets/js/instructor_subscription.js"></script>

<?php include '../../includes/instructor/instructor_footer.php'; ?>
