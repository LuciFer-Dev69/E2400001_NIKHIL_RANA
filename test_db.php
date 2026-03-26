<?php
require_once 'config/db.php';

echo "--- SkillEdu System Health Check ---\n";

try {
    // Check connection
    if ($pdo) {
        echo "[SUCCESS] Database connected to: " . $db . "\n";
    }

    // Check key tables
    $tables = ['users', 'courses', 'categories', 'enrollments', 'notifications'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "[TABLE] $table: $count records found.\n";
    }

    echo "[SUCCESS] All core tables are accessible.\n";

}
catch (Exception $e) {
    echo "[ERROR] Health check failed: " . $e->getMessage() . "\n";
}
?>
