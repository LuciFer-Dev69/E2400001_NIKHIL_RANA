<?php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if (!$course_id) {
    die("Invalid course.");
}

try {
    // Verify 100% completion
    $stmt = $pdo->prepare("
        SELECT c.title, u.full_name as instructor_name, e.progress_percent, e.enrolled_at 
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN users u ON c.instructor_id = u.id
        WHERE e.student_id = ? AND e.course_id = ? AND e.progress_percent = 100
    ");
    $stmt->execute([$user_id, $course_id]);
    $data = $stmt->fetch();

    if (!$data) {
        die("You have not completed this course yet.");
    }

    // Get student name
    $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $student_name = $stmt->fetchColumn();

}
catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$date_completed = date('F j, Y');
$cert_id = "UC-" . strtoupper(substr(md5($user_id . $course_id . time()), 0, 12));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion - <?php echo htmlspecialchars($data['title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Roboto', sans-serif;
            color: #1c1d1f;
        }
        .cert-container {
            width: 1056px; /* 11 inches at 96dpi */
            height: 816px; /* 8.5 inches at 96dpi (Landscape Letter) */
            background: #ffffff;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 20px solid #1c1d1f;
            box-sizing: border-box;
            background-image: radial-gradient(#f7f9fa 20%, transparent 20%),
                              radial-gradient(#f7f9fa 20%, transparent 20%);
            background-color: #ffffff;
            background-position: 0 0, 10px 10px;
            background-size: 20px 20px;
        }
        .cert-container::before {
            content: '';
            position: absolute;
            top: 5px; left: 5px; right: 5px; bottom: 5px;
            border: 2px solid #d1d7dc;
            z-index: 1;
        }
        .cert-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 60px;
            background: rgba(255, 255, 255, 0.95);
            width: 80%;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .logo {
            font-size: 32px;
            font-weight: 800;
            color: #1c1d1f;
            margin-bottom: 20px;
        }
        .logo span { color: #e74c3c; }
        .cert-title {
            font-family: 'Playfair Display', serif;
            font-size: 54px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #1c1d1f;
            margin: 0 0 20px 0;
        }
        .cert-subtitle {
            font-size: 18px;
            color: #6a6f73;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .student-name {
            font-family: 'Playfair Display', serif;
            font-size: 46px;
            font-weight: 700;
            color: #e74c3c;
            margin: 0 0 30px 0;
            border-bottom: 1px solid #d1d7dc;
            padding-bottom: 10px;
            display: inline-block;
            min-width: 50%;
        }
        .course-text {
            font-size: 18px;
            color: #2d2f31;
            margin-bottom: 10px;
        }
        .course-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            color: #1c1d1f;
            margin: 0 0 40px 0;
        }
        .cert-footer {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }
        .signature-block, .date-block {
            width: 200px;
        }
        .line {
            border-top: 1px solid #1c1d1f;
            margin-bottom: 10px;
        }
        .label {
            font-size: 14px;
            color: #6a6f73;
            font-weight: 700;
        }
        .val {
            font-size: 16px;
            color: #1c1d1f;
            margin-bottom: 8px;
        }
        .cert-meta {
            position: absolute;
            bottom: 20px;
            left: 30px;
            font-size: 12px;
            color: #6a6f73;
            z-index: 2;
        }
        
        .print-btn {
            position: fixed;
            top: 30px;
            right: 30px;
            background: #1c1d1f;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .print-btn:hover { background: #333; }
        
        @media print {
            body { background: white; }
            .cert-container { 
                box-shadow: none; 
                border: 15px solid #1c1d1f; /* slightly thinner for print */
                width: 100%; height: 100vh; 
            }
            .print-btn { display: none; }
            @page { size: landscape; margin: 0; }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">
        <i class="fa fa-print"></i> Download / Print PDF
    </button>

    <div class="cert-container">
        <div class="cert-meta">
            Certificate ID: <?php echo $cert_id; ?><br>
            Reference: URL/EMS1/verify
        </div>
        
        <div class="cert-content">
            <div class="logo">Skill<span>Edu</span></div>
            <h1 class="cert-title">Certificate of Completion</h1>
            <div class="cert-subtitle">This is to certify that</div>
            
            <h2 class="student-name"><?php echo htmlspecialchars($student_name); ?></h2>
            
            <div class="course-text">has successfully completed the online course</div>
            <h3 class="course-title"><?php echo htmlspecialchars($data['title']); ?></h3>
            
            <div class="cert-footer">
                <div class="date-block">
                    <div class="val"><?php echo $date_completed; ?></div>
                    <div class="line"></div>
                    <div class="label">Date</div>
                </div>
                
                <div style="width: 120px; height: 120px; position: absolute; left: 50%; transform: translateX(-50%); bottom: -20px;">
                    <!-- A simple CSS seal -->
                    <div style="width: 100px; height: 100px; background: #e74c3c; border-radius: 50%; border: 4px dashed #f1c40f; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 14px; text-transform: uppercase; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.2); margin: 0 auto; line-height: 1.2;">
                        Official<br>Certified
                    </div>
                </div>

                <div class="signature-block">
                    <div class="val" style="font-family: 'Playfair Display', serif; font-style: italic; font-size: 24px; margin-bottom: 2px; color: #1c1d1f;"><?php echo htmlspecialchars($data['instructor_name']); ?></div>
                    <div class="line"></div>
                    <div class="label">Instructor</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
