<?php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$portal_type = 'student';

// Ensure current user has gamification row
$pdo->prepare("INSERT IGNORE INTO gamification_stats (user_id, xp, streak_days, last_login_date) VALUES (?, 0, 1, CURDATE())")->execute([$user_id]);

// Get Global Leaderboard
$stmt = $pdo->query("
    SELECT u.id, u.full_name, u.profile_img, g.xp, g.streak_days,
           (SELECT COUNT(*) FROM badges_earned b WHERE b.user_id = u.id) as badge_count
    FROM gamification_stats g 
    JOIN users u ON g.user_id = u.id
    ORDER BY g.xp DESC
    LIMIT 20
");
$leaderboard = $stmt->fetchAll();

// Get Current User gamification
$stmt = $pdo->prepare("
    SELECT g.xp, g.streak_days, 
           (SELECT COUNT(*) FROM badges_earned b WHERE b.user_id = g.user_id) as badge_count
    FROM gamification_stats g WHERE g.user_id = ?
");
$stmt->execute([$user_id]);
$my_stats = $stmt->fetch();

$root = "../../";
$page_title = 'Global Leaderboard';
include '../../includes/portal_header.php';
?>
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
            <div>
                <h1 style="font-size: 32px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Global Leaderboard</h1>
                <p style="color: var(--gray-color); font-size: 16px;">Compete with students worldwide and climb the ranks! 🏆</p>
            </div>
            <div style="background: linear-gradient(135deg, var(--white) 0%, var(--border-color) 100%); padding: 10px 20px; border-radius: 50px; color: #fff; display: flex; align-items: center; gap: 15px; box-shadow: var(--shadow);">
                <div style="font-weight: 700;">My Rank: 
                    <?php
// simple rank calc
$rank = 1;
foreach ($leaderboard as $idx => $l) {
    if ($l['id'] == $user_id) {
        $rank = $idx + 1;
        break;
    }
}
if ($rank <= 20) {
    echo "#" . $rank;
}
else {
    echo "Unranked";
}
?>
                </div>
                <div style="width: 1px; height: 15px; background: rgba(255,255,255,0.3);"></div>
                <div style="color: #FF416C; font-weight: 800;"><i class="fa fa-star"></i> <?php echo number_format($my_stats['xp'] ?? 0); ?> XP</div>
            </div>
        </div>

        <div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow); overflow: hidden;">
            <div style="display: grid; grid-template-columns: 80px 3fr 1fr 1fr 1fr; padding: 15px 25px; background: var(--light-gray); font-weight: 700; color: var(--gray-color); font-size: 13px; text-transform: uppercase;">
                <div>Rank</div>
                <div>Student</div>
                <div style="text-align: center;">XP</div>
                <div style="text-align: center;">Badges</div>
                <div style="text-align: center;">Streak</div>
            </div>
            
            <?php
$i = 1;
foreach ($leaderboard as $user):
    $is_me = ($user['id'] == $user_id);

    // Rank Styling
    $rank_style = "font-size: 16px; font-weight: 700; color: var(--gray-color);";
    $row_bg = $is_me ? "background: var(--bg-page); border-left: 4px solid #FF416C;" : "border-bottom: 1px solid var(--border-color);";
    if ($i == 1)
        $rank_style = "font-size: 24px; color: #f1c40f; text-shadow: 0 2px 4px rgba(241,196,15,0.3);";
    else if ($i == 2)
        $rank_style = "font-size: 20px; color: #bdc3c7;";
    else if ($i == 3)
        $rank_style = "font-size: 18px; color: #cd7f32;";
?>
            <div style="display: grid; grid-template-columns: 80px 3fr 1fr 1fr 1fr; padding: 20px 25px; align-items: center; <?php echo $row_bg; ?> transition: background 0.2s;">
                <div style="<?php echo $rank_style; ?>">
                    <?php if ($i <= 3): ?><i class="fa fa-trophy"></i><?php
    else:
        echo "#" . $i;
    endif; ?>
                </div>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px;">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 16px; color: var(--dark-color);"><?php echo htmlspecialchars($user['full_name']); ?> <?php if ($is_me)
        echo '<span style="font-size: 10px; background: #FF416C; color: white; padding: 2px 6px; border-radius: 10px; margin-left: 5px;">YOU</span>'; ?></div>
                        <div style="font-size: 12px; color: var(--gray-color);">Learning since 2026</div>
                    </div>
                </div>
                <div style="text-align: center; font-weight: 800; font-size: 18px; color: #FF416C;">
                    <?php echo number_format($user['xp']); ?>
                </div>
                <div style="text-align: center;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%; background: var(--light-gray); color: #2ecc71; font-weight: 800; font-size: 14px;">
                        <?php echo $user['badge_count']; ?>
                    </div>
                </div>
                <div style="text-align: center;">
                    <div style="display: inline-flex; align-items: center; gap: 5px; color: #e67e22; font-weight: 800; font-size: 15px;">
                        <i class="fa fa-fire"></i> <?php echo $user['streak_days']; ?>
                    </div>
                </div>
            </div>
            <?php
    $i++;
endforeach;
?>
        </div>
        
    </div>
<?php include '../../includes/portal_footer.php'; ?>
