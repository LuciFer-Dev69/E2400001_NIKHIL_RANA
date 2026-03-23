<?php
require_once '../../config/db.php';
session_start();

// 1. Authentication Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if (!$course_id) {
    echo "No course selected.";
    exit();
}

// 2. Enrollment Verification
try {
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    $enrollment = $stmt->fetch();

    if (!$enrollment) {
        echo "You are not enrolled in this course.";
        exit();
    }

    // 3. Fetch Course & Instructor
    $stmt = $pdo->prepare("
        SELECT c.*, u.full_name as instructor_name 
        FROM courses c 
        JOIN users u ON c.instructor_id = u.id 
        WHERE c.id = ?
    ");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    // 4. Fetch Lessons
    $stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_num ASC");
    $stmt->execute([$course_id]);
    $lessons = $stmt->fetchAll();

    if (empty($lessons)) {
        echo "No lessons found for this course.";
        exit();
    }

    // 5. Fetch Progress
    $stmt = $pdo->prepare("SELECT lesson_id, status FROM user_lesson_progress WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    $progress_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 6. Identify Current Lesson
    $current_lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : $lessons[0]['id'];
    $current_lesson = null;
    foreach ($lessons as $lesson) {
        if ($lesson['id'] == $current_lesson_id) {
            $current_lesson = $lesson;
            break;
        }
    }
    if (!$current_lesson)
        $current_lesson = $lessons[0];

    // 7. Fetch Notes for current lesson
    $stmt = $pdo->prepare("SELECT * FROM user_notes WHERE user_id = ? AND lesson_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id, $current_lesson_id]);
    $notes = $stmt->fetchAll();

}
catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$base_url = "../../";
$portal_context = 'student';
include '../../includes/header.php';

?>
<style>
    /* Course Player Specific Focus Mode */
    .player-container {
        height: calc(100vh - 60px);
        display: flex;
        overflow: hidden;
    }
</style>

<div class="player-container">
        <!-- Main Content Area -->
        <main class="player-main">
            <div class="video-section">
                <!-- If it's a video lesson -->
                <?php if ($current_lesson['video_url']): ?>
                    <div style="text-align: center;">
                        <i class="fa fa-play-circle" style="font-size: 80px; margin-bottom: 20px; opacity: 0.5;"></i>
                        <h2 style="font-size: 24px;">Lecture Video Loading...</h2>
                        <p style="color: #6a6f73;"><?php echo $current_lesson['video_url']; ?></p>
                    </div>
                <!-- Placeholder for non-video content -->
                <?php
else: ?>
                    <div style="padding: 60px; color: #1c1d1f; background: white; width: 100%; height: 100%; overflow-y: auto;">
                        <h1><?php echo $current_lesson['title']; ?></h1>
                        <p>This is a text-based lesson content.</p>
                    </div>
                <?php
endif; ?>
            </div>

            <!-- Learning Tools Tabs -->
            <div class="player-tabs-container">
                <div class="player-tabs">
                    <div class="player-tab active">Overview</div>
                    <div class="player-tab">Notes</div>
                    <div class="player-tab">Q&A</div>
                    <div class="player-tab">Resources</div>
                </div>
                
                <div class="tab-content" id="tab-overview" style="padding-top: 25px;">
                    <h2 style="font-size: 24px; margin-bottom: 15px;">About this lecture</h2>
                    <p style="color: #2d2f31; line-height: 1.6;">
                        This lecture covers the fundamental concepts of <?php echo $current_lesson['title']; ?>. 
                    </p>
                </div>

                <div class="tab-content" id="tab-notes" style="padding-top: 25px; display: none;">
                    <h2 style="font-size: 24px; margin-bottom: 15px;">Create a new note</h2>
                    <div style="margin-bottom: 20px;">
                        <textarea id="note-input" style="width: 100%; height: 100px; padding: 15px; border: 1px solid #d1d7dc; border-radius: 4px; font-family: inherit;" placeholder="Type your note here..."></textarea>
                        <button id="save-note-btn" class="btn btn-secondary" style="margin-top: 10px; background: #1c1d1f; color: white;">Save note</button>
                    </div>
                    <div id="notes-list">
                        <h4 style="margin-bottom: 15px;">Your notes</h4>
                        <?php if (empty($notes)): ?>
                            <p id="no-notes-msg" style="color: #6a6f73; font-style: italic;">No notes yet for this lecture.</p>
                        <?php
else: ?>
                            <?php foreach ($notes as $note): ?>
                                <div class="note-item" style="padding: 15px; background: #f8f9fa; border-radius: 4px; margin-bottom: 10px; border-left: 4px solid var(--primary-color);">
                                    <div style="font-size: 12px; color: #6a6f73; margin-bottom: 5px;">
                                        <?php echo date('M d, Y', strtotime($note['created_at'])); ?> 
                                        <?php if ($note['video_timestamp']): ?> 
                                            at <?php echo floor($note['video_timestamp'] / 60); ?>:<?php echo sprintf('%02d', $note['video_timestamp'] % 60); ?>
                                        <?php
        endif; ?>
                                    </div>
                                    <div style="font-size: 14px; line-height: 1.5;"><?php echo nl2br(htmlspecialchars($note['note_text'])); ?></div>
                                </div>
                            <?php
    endforeach; ?>
                        <?php
endif; ?>
                    </div>
                </div>

                <div class="tab-content" id="tab-qa" style="padding-top: 25px; display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="font-size: 24px;">Questions in this course</h2>
                        <button id="ask-qt-btn" class="btn btn-secondary" style="border: 1px solid #1c1d1f; height: 40px;">Ask a new question</button>
                    </div>

                    <div id="ask-qt-form" style="display: none; margin-bottom: 25px; background: #f7f9fa; padding: 20px; border-radius: 8px; border: 1px solid #d1d7dc;">
                        <h4 style="margin-bottom: 15px; font-size: 16px;">Ask a question</h4>
                        <textarea id="qt-input" style="width: 100%; height: 80px; padding: 12px; border: 1px solid #d1d7dc; border-radius: 4px; font-family: inherit; margin-bottom: 15px; font-size: 14px;" placeholder="What do you want to ask about this specific lecture?"></textarea>
                        <div style="display: flex; gap: 10px;">
                            <button id="submit-qt-btn" class="btn btn-primary" style="background: #1c1d1f; color: white;">Post Question</button>
                            <button id="cancel-qt-btn" class="btn btn-secondary">Cancel</button>
                        </div>
                    </div>

                    <div id="qa-list">
                        <div class="qa-placeholder">
                            <p style="color: #6a6f73; font-style: italic;">Loading questions...</p>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="tab-resources" style="padding-top: 25px; display: none;">
                    <h2 style="font-size: 24px; margin-bottom: 15px;">Resources</h2>
                    <div class="resource-list">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border: 1px solid #d1d7dc; border-radius: 4px; margin-bottom: 10px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fa fa-file-pdf" style="color: #ff3c3c; font-size: 20px;"></i>
                                <span style="font-weight: 700;">Lecture Slides.pdf</span>
                            </div>
                            <a href="#" class="btn btn-secondary" style="padding: 6px 15px; font-size: 13px;">Download</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Curriculum Sidebar -->
        <aside class="player-sidebar">
            <div class="curriculum-header">
                Course content
            </div>
            
            <div class="curriculum-sections">
                <!-- Section 1 (Static for now) -->
                <div class="curriculum-section">
                    <div class="curriculum-section-title">
                        <span>Section 1: Introduction</span>
                        <i class="fa fa-chevron-down"></i>
                    </div>
                    
                    <div class="lecture-list">
                        <?php foreach ($lessons as $lesson): ?>
                            <?php
    $is_active = $lesson['id'] == $current_lesson_id;
    $status = $progress_data[$lesson['id']] ?? 'not_started';
?>
                            <a href="?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $lesson['id']; ?>" class="lecture-item <?php echo $is_active ? 'active' : ''; ?>">
                                <div class="check-circle <?php echo $status == 'completed' ? 'completed' : ''; ?>">
                                    <?php if ($status == 'completed')
        echo '<i class="fa fa-check"></i>'; ?>
                                </div>
                                <div class="lecture-info">
                                    <h4 style="font-weight: <?php echo $is_active ? '700' : '400'; ?>;">
                                        <?php echo $lesson['order_num']; ?>. <?php echo $lesson['title']; ?>
                                    </h4>
                                    <div class="lecture-meta">
                                        <i class="fa fa-play-circle" style="font-size: 11px;"></i>
                                        <span><?php echo $lesson['duration_mins']; ?>min</span>
                                    </div>
                                </div>
                            </a>
                        <?php
endforeach; ?>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        function updateProgress(lessonId, status) {
            const courseId = <?php echo $course_id; ?>;
            
            fetch('api/update_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    lesson_id: lessonId,
                    course_id: courseId,
                    status: status
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log('Progress updated:', data.progress_percent + '%');
                    // Update progress display in header
                    document.querySelector('.progress-text-header').innerText = 'Your progress: ' + data.progress_percent + '%';
                }
            })
            .catch(err => console.error('Error updating progress:', err));
        }

        document.querySelectorAll('.check-circle').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const item = btn.closest('.lecture-item');
                const lessonId = new URL(item.href).searchParams.get('lesson_id');
                const isCompleted = btn.classList.contains('completed');
                const newStatus = isCompleted ? 'in_progress' : 'completed';

                btn.classList.toggle('completed');
                btn.innerHTML = btn.classList.contains('completed') ? '<i class="fa fa-check"></i>' : '';
                
                updateProgress(lessonId, newStatus);
            }
        });

        // Tab switching logic
        let qaLoaded = false;
        
        function loadQA() {
            const lessonId = <?php echo $current_lesson_id; ?>;
            const qaList = document.getElementById('qa-list');
            
            fetch(`api/qa_system.php?action=get&lesson_id=${lessonId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderQA(data.questions);
                }
            })
            .catch(err => {
                qaList.innerHTML = '<p>Error loading questions.</p>';
                console.error(err);
            });
        }
        
        function renderQA(questions) {
            const qaList = document.getElementById('qa-list');
            if (questions.length === 0) {
                qaList.innerHTML = `
                    <div class="qa-placeholder" style="text-align: center; padding: 20px;">
                        <p style="color: #6a6f73; font-style: italic;">No questions have been asked yet for this lecture. Be the first!</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            questions.forEach(q => {
                html += `
                    <div class="qa-item" style="padding: 15px; border: 1px solid #d1d7dc; border-radius: 8px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <div style="font-weight: 700; font-size: 14px;">${q.user_name}</div>
                            <div style="font-size: 12px; color: #6a6f73;">${q.created_at}</div>
                        </div>
                        <p style="margin-bottom: 10px; font-size: 15px;">${q.question_text}</p>
                        
                        <div class="qa-answers" style="margin-left: 20px; padding-left: 15px; border-left: 2px solid #e0e0e0;">
                `;
                if (q.answers.length > 0) {
                    q.answers.forEach(a => {
                        html += `
                            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #f1f3f5;">
                                <div style="display: flex; gap: 8px; margin-bottom: 5px; align-items: center;">
                                    <span style="font-weight: 700; font-size: 13px;">${a.user_name}</span>
                                    ${a.is_instructor == 1 ? '<span style="background: #2ecc71; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">Instructor</span>' : ''}
                                    <span style="font-size: 11px; color: #6a6f73;">${a.created_at}</span>
                                </div>
                                <p style="font-size: 14px;">${a.answer_text}</p>
                            </div>
                        `;
                    });
                } else {
                    html += `<p style="font-size: 13px; color: #6a6f73; font-style: italic; margin-top: 10px;">No answers yet.</p>`;
                }
                
                html += `
                        </div>
                    </div>
                `;
            });
            qaList.innerHTML = html;
        }

        document.querySelectorAll('.player-tab').forEach(tab => {
            tab.onclick = () => {
                document.querySelector('.player-tab.active').classList.remove('active');
                tab.classList.add('active');
                
                // Hide all content
                document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
                
                // Show selected content
                const tabName = tab.innerText.toLowerCase();
                if (tabName === 'overview') document.getElementById('tab-overview').style.display = 'block';
                if (tabName === 'notes') document.getElementById('tab-notes').style.display = 'block';
                if (tabName === 'q&a') {
                    document.getElementById('tab-qa').style.display = 'block';
                    if (!qaLoaded) {
                        loadQA();
                        qaLoaded = true;
                    }
                }
                if (tabName === 'resources') document.getElementById('tab-resources').style.display = 'block';
            }
        });

        document.getElementById('ask-qt-btn').onclick = () => {
            document.getElementById('ask-qt-form').style.display = 'block';
            document.getElementById('ask-qt-btn').style.display = 'none';
        };
        
        document.getElementById('cancel-qt-btn').onclick = () => {
             document.getElementById('ask-qt-form').style.display = 'none';
             document.getElementById('ask-qt-btn').style.display = 'block';
             document.getElementById('qt-input').value = '';
        };
        
        document.getElementById('submit-qt-btn').onclick = () => {
            const btn = document.getElementById('submit-qt-btn');
            const qtText = document.getElementById('qt-input').value;
            const lessonId = <?php echo $current_lesson_id; ?>;
            const courseId = <?php echo $course_id; ?>;
            
            if (!qtText.trim()) return;
            
            btn.disabled = true;
            btn.innerText = 'Posting...';
            
            fetch('api/qa_system.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'ask', course_id: courseId, lesson_id: lessonId, question_text: qtText })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('qt-input').value = '';
                    document.getElementById('ask-qt-form').style.display = 'none';
                    document.getElementById('ask-qt-btn').style.display = 'block';
                    loadQA(); // reload list
                }
                btn.disabled = false;
                btn.innerText = 'Post Question';
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerText = 'Post Question';
            });
        };


        document.getElementById('save-note-btn').onclick = () => {
            const btn = document.getElementById('save-note-btn');
            const noteText = document.getElementById('note-input').value;
            const lessonId = <?php echo $current_lesson_id; ?>;
            const courseId = <?php echo $course_id; ?>;

            if (!noteText.trim()) return;

            btn.disabled = true;
            btn.innerText = 'Saving...';

            fetch('api/manage_notes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'save',
                    course_id: courseId,
                    lesson_id: lessonId,
                    note_text: noteText
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const notesList = document.getElementById('notes-list');
                    const noNotesMsg = document.getElementById('no-notes-msg');
                    if (noNotesMsg) noNotesMsg.remove();

                    const newNote = document.createElement('div');
                    newNote.className = 'note-item';
                    newNote.style = "padding: 15px; background: #f8f9fa; border-radius: 4px; margin-bottom: 10px; border-left: 4px solid var(--primary-color);";
                    newNote.innerHTML = `
                        <div style="font-size: 12px; color: #6a6f73; margin-bottom: 5px;">Just now</div>
                        <div style="font-size: 14px; line-height: 1.5;">${noteText.replace(/\n/g, '<br>')}</div>
                    `;
                    
                    const h4 = notesList.querySelector('h4');
                    h4.after(newNote);

                    document.getElementById('note-input').value = '';
                }
                btn.disabled = false;
                btn.innerText = 'Save note';
            })
            .catch(err => {
                console.error('Error saving note:', err);
                btn.disabled = false;
                btn.innerText = 'Save note';
            });
        };
    </script>
</body>
<?php include '../../includes/footer.php'; ?>
