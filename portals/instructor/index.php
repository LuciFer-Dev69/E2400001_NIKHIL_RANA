<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Instructor Dashboard';
include '../../includes/instructor/instructor_header.php';

$inst_id = $_SESSION['user_id'];
?>
<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
        <p style="color: var(--gray-color); font-size: 15px;">Here is your Creator Analytics overview for today.</p>
    </div>
    <div>
        <a href="create_course.php" class="btn btn-primary" style="padding: 10px 20px; font-weight: 700; background: #9b59b6; border-color: #9b59b6;">
            <i class="fa fa-magic" style="margin-right: 8px;"></i> Create New Course
        </a>
    </div>
</div>

<!-- STAT CARDS -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px;">
    <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
        <p style="color: var(--gray-color); font-size: 15px;">Here is your Creator Analytics overview for today.</p>
    </div>
    <div>
        <a href="create_course.php" class="btn btn-primary" style="padding: 10px 20px; font-weight: 700; background: #9b59b6; border-color: #9b59b6;">
            <i class="fa fa-magic" style="margin-right: 8px;"></i> Create New Course
        </a>
    </div>
</div>

            <div style="font-size: 13px; font-weight: 800; color: var(--gray-color); text-transform: uppercase;">Total Students</div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(52, 152, 219, 0.1); color: #3498db; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa fa-users"></i>
            </div>
        </div>
        <div id="total-students-val" class="stat-value" style="font-size: 32px; font-weight: 800; color: var(--dark-color);">0</div>
        <div style="font-size: 12px; color: #2ecc71; margin-top: 8px; font-weight: 700;"><i class="fa fa-arrow-up"></i> Unique learners</div>
    </div>

    <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
            <div style="font-size: 13px; font-weight: 800; color: var(--gray-color); text-transform: uppercase;">Published Courses</div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(155, 89, 182, 0.1); color: #9b59b6; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa fa-video"></i>
            </div>
        </div>
        <div id="published-courses-val" class="stat-value" style="font-size: 32px; font-weight: 800; color: var(--dark-color);">0</div>
        <div style="font-size: 12px; color: var(--gray-color); margin-top: 8px;">Active on platform</div>
    </div>

    <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
            <div style="font-size: 13px; font-weight: 800; color: var(--gray-color); text-transform: uppercase;">Avg Completion</div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(46, 204, 113, 0.1); color: #2ecc71; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
        <div id="avg-completion-val" class="stat-value" style="font-size: 32px; font-weight: 800; color: var(--dark-color);">0%</div>
        <div style="font-size: 12px; color: var(--gray-color); margin-top: 8px;">Across all active learners</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <!-- Analytics Charts -->
    <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--dark-color);">Student Enrollment Trends</h3>
            <div style="font-size: 12px; color: var(--gray-color); font-weight: 700;"><i class="fa fa-circle" style="color: #9b59b6; margin-right: 5px;"></i> Last 7 Days</div>
        </div>
        <div style="height: 300px; position: relative;">
            <canvas id="enrollmentChart"></canvas>
        </div>
    </div>

    <!-- Quick Actions / Distribution -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--dark-color); margin-bottom: 20px;">Student Distribution</h3>
            <div style="height: 220px; position: relative;">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>

        <!-- Subscription Promo -->
        <div style="background: linear-gradient(135deg, #FF4B2B, #FF416C); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 10px 20px rgba(255, 65, 108, 0.3); margin-top: 20px; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -20px; top: -20px; font-size: 100px; opacity: 0.1; transform: rotate(15deg);"><i class="fa fa-rocket"></i></div>
            <h3 style="font-size: 20px; font-weight: 800; margin-bottom: 10px;">Upgrade to Pro</h3>
            <p style="font-size: 14px; opacity: 0.9; margin-bottom: 20px; max-width: 80%;">Unlock unlimited courses, lower platform fees, and advanced student analytics with our Pro and Enterprise plans.</p>
            <a href="subscription.php" class="btn" style="background: white; color: #FF416C; font-weight: 800; padding: 10px 25px; border-radius: 8px; text-decoration: none; display: inline-block; transition: all 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">View Plans</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
<script src="<?php echo $root; ?>assets/js/instructor_analytics.js" defer></script>

<?php include '../../includes/instructor/instructor_footer.php'; ?>
