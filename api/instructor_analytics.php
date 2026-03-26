<?php
/**
 * api/instructor_analytics.php
 * Provides JSON data for the instructor's dashboard charts and stats.
 */
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$inst_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'dashboard_stats';

try {
    if ($action === 'dashboard_stats') {
        // High level stats
        $stats = [
            'total_students' => 0,
            'published_courses' => 0,
            'pending_courses' => 0,
            'avg_completion' => 0,
            'engagement_score' => 88 // Simulated
        ];

        // Total Students
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT e.student_id) FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.instructor_id = ?");
        $stmt->execute([$inst_id]);
        $stats['total_students'] = (int)$stmt->fetchColumn();

        // Courses count
        $stmt = $pdo->prepare("SELECT 
            SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            COUNT(*) as total
            FROM courses WHERE instructor_id = ?");
        $stmt->execute([$inst_id]);
        $cData = $stmt->fetch();
        $stats['published_courses'] = (int)$cData['published'];
        $stats['pending_courses'] = (int)$cData['pending'];
        $stats['total_courses'] = (int)$cData['total'];

        // Enrollment Trends (7 days)
        $trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('D', strtotime($date));

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.instructor_id = ? AND DATE(e.enrolled_at) = ?");
            $stmt->execute([$inst_id, $date]);
            $count = (int)$stmt->fetchColumn();

            $trends[] = ['label' => $label, 'value' => $count];
        }

        // Student Distribution by Course
        $distribution = [];
        $stmt = $pdo->prepare("SELECT title, (SELECT COUNT(*) FROM enrollments WHERE course_id = courses.id) as students FROM courses WHERE instructor_id = ? AND status = 'published' LIMIT 5");
        $stmt->execute([$inst_id]);
        $distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'trends' => $trends,
            'distribution' => $distribution
        ]);
    }
}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
