<?php
require_once '../../config/db.php';
session_start();

$root = "../../";
$page_title = 'Create New Course';
include '../../includes/instructor/instructor_header.php';

$inst_id = $_SESSION['user_id'];

// Fetch all categories for the dropdown
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<div style="max-width: 780px; margin: 0 auto;">
    <!-- Wizard Header -->
    <div style="margin-bottom: 35px;">
        <a href="courses.php" style="color: var(--gray-color); font-size: 14px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 20px;"><i class="fa fa-arrow-left"></i> My Courses</a>
        <h1 style="font-size: 26px; font-weight: 800; color: var(--dark-color); margin-bottom: 8px;">Create a New Course</h1>
        <p style="color: var(--gray-color);">Fill in the details below to create your course. It will be saved as a Draft until you submit for Admin approval to publish.</p>
    </div>

    <!-- Progress Steps -->
    <div style="display: flex; align-items: center; gap: 0; margin-bottom: 40px;" id="wizard-steps">
        <div class="wizard-step active" id="step-indicator-1" style="flex: 1; text-align: center; position: relative;">
            <div style="width: 36px; height: 36px; border-radius: 50%; background: #9b59b6; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 800;">1</div>
            <div style="font-size: 12px; font-weight: 700; margin-top: 6px; color: #9b59b6;">Course Basics</div>
        </div>
        <div style="flex: 1; height: 2px; background: var(--border-color); margin-top: -20px;"></div>
        <div class="wizard-step" id="step-indicator-2" style="flex: 1; text-align: center; position: relative;">
            <div id="step2-circle" style="width: 36px; height: 36px; border-radius: 50%; background: var(--light-gray); border: 2px solid var(--border-color); color: var(--gray-color); display: inline-flex; align-items: center; justify-content: center; font-weight: 800;">2</div>
            <div id="step2-label" style="font-size: 12px; font-weight: 700; margin-top: 6px; color: var(--gray-color);">Curriculum</div>
        </div>
        <div style="flex: 1; height: 2px; background: var(--border-color); margin-top: -20px;"></div>
        <div class="wizard-step" id="step-indicator-3" style="flex: 1; text-align: center; position: relative;">
            <div id="step3-circle" style="width: 36px; height: 36px; border-radius: 50%; background: var(--light-gray); border: 2px solid var(--border-color); color: var(--gray-color); display: inline-flex; align-items: center; justify-content: center; font-weight: 800;">3</div>
            <div id="step3-label" style="font-size: 12px; font-weight: 700; margin-top: 6px; color: var(--gray-color);">Publish</div>
        </div>
    </div>

    <!-- STEP 1: Course Basics -->
    <div id="wizard-step-1">
        <form id="basics-form">
            <div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); padding: 30px; margin-bottom: 20px; box-shadow: var(--shadow);">
                <h3 style="font-weight: 800; font-size: 18px; color: var(--dark-color); margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color);">Course Information</h3>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; font-size: 14px; color: var(--dark-color); margin-bottom: 8px;">Course Title <span style="color: #e74c3c;">*</span></label>
                    <input type="text" id="course-title" required maxlength="255" placeholder="e.g. Complete Web Development Bootcamp" style="width: 100%; padding: 13px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 15px; font-family: inherit;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; font-size: 14px; color: var(--dark-color); margin-bottom: 8px;">Course Description <span style="color: #e74c3c;">*</span></label>
                    <textarea id="course-desc" required rows="5" placeholder="What will students learn? Why should they enroll? (Minimum 100 characters)" style="width: 100%; padding: 13px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 14px; font-family: inherit; resize: vertical;"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-weight: 700; font-size: 14px; color: var(--dark-color); margin-bottom: 8px;">Category <span style="color: #e74c3c;">*</span></label>
                        <select id="course-category" style="width: 100%; padding: 13px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 14px; font-family: inherit; appearance: auto;">
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php
endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; font-size: 14px; color: var(--dark-color); margin-bottom: 8px;">Difficulty Level</label>
                        <select id="course-difficulty" style="width: 100%; padding: 13px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 14px; font-family: inherit; appearance: auto;">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; font-weight: 700; font-size: 14px; color: var(--dark-color); margin-bottom: 8px;">Pricing</label>
                        <select id="course-pricing-type" onchange="togglePriceField()" style="width: 100%; padding: 13px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 14px; font-family: inherit; appearance: auto;">
                            <option value="free">Free</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                    <div id="price-field" style="display: none;">
                        <label style="display: block; font-weight: 700; font-size: 14px; color: var(--dark-color); margin-bottom: 8px;">Price (USD)</label>
                        <input type="number" id="course-price" min="1" step="0.01" placeholder="e.g. 29.99" style="width: 100%; padding: 13px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-size: 14px; font-family: inherit;">
                    </div>
                </div>
            </div>

            <div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); padding: 30px; margin-bottom: 20px; box-shadow: var(--shadow);">
                <h3 style="font-weight: 800; font-size: 18px; color: var(--dark-color); margin-bottom: 20px;">Course Thumbnail</h3>
                <div id="thumbnail-dropzone" style="border: 2px dashed var(--border-color); border-radius: 8px; padding: 40px; text-align: center; cursor: pointer; background: var(--light-gray); transition: all 0.2s;" onclick="document.getElementById('thumbnail-file').click()">
                    <i class="fa fa-cloud-upload-alt" style="font-size: 32px; color: var(--gray-color); margin-bottom: 10px;"></i>
                    <div id="thumbnail-text" style="font-weight: 700; color: var(--gray-color);">Click to upload thumbnail (JPG, PNG)</div>
                </div>
                <input type="file" id="thumbnail-file" accept="image/*" style="display: none;" onchange="previewThumbnail(this)">
            </div>

            <div id="basics-msg" style="padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14px; display: none; margin-bottom: 15px;"></div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="button" onclick="submitBasics()" id="basics-btn" class="btn btn-primary" style="padding: 13px 35px; font-size: 16px; font-weight: 800; background: #9b59b6; border-color: #9b59b6;">
                    Next: Build Curriculum <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- STEP 2: Curriculum Builder -->
    <div id="wizard-step-2" style="display: none;">
        <div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); padding: 30px; margin-bottom: 20px; box-shadow: var(--shadow);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color);">
                <h3 style="font-weight: 800; font-size: 18px; color: var(--dark-color);">Course Curriculum</h3>
                <button type="button" onclick="addLesson()" class="btn btn-primary" style="padding: 8px 18px; background: #9b59b6; border-color: #9b59b6;"><i class="fa fa-plus"></i> Add Lesson</button>
            </div>

            <div id="lesson-list">
                <div style="text-align: center; padding: 40px; color: var(--gray-color);" id="no-lessons-placeholder">
                    <i class="fa fa-layer-group" style="font-size: 40px; margin-bottom: 15px; opacity: 0.4;"></i>
                    <p style="font-weight: 700;">No lessons yet. Click "Add Lesson" to start building your curriculum!</p>
                </div>
            </div>
        </div>

        <div id="curriculum-msg" style="padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14px; display: none; margin-bottom: 15px;"></div>

        <div style="display: flex; justify-content: space-between;">
            <button type="button" onclick="goBack()" class="btn" style="padding: 13px 25px; background: none; border: 1px solid var(--border-color); color: var(--dark-color);">
                <i class="fa fa-arrow-left" style="margin-right: 8px;"></i> Back
            </button>
            <button onclick="goToPublish()" class="btn btn-primary" style="padding: 13px 35px; font-size: 16px; font-weight: 800; background: #9b59b6; border-color: #9b59b6;">
                Next: Review & Publish <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </div>
    </div>

    <!-- STEP 3: Publish -->
    <div id="wizard-step-3" style="display: none;">
        <div style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); padding: 30px; margin-bottom: 20px; box-shadow: var(--shadow); text-align: center;">
            <div style="font-size: 60px; margin-bottom: 20px;">🎉</div>
            <h2 id="publish-course-title" style="font-size: 24px; font-weight: 800; color: var(--dark-color); margin-bottom: 15px;">Course Ready!</h2>
            <p style="color: var(--gray-color); max-width: 450px; margin: 0 auto 30px auto; line-height: 1.7;">Your course has been saved as a draft. Click <strong>Submit for Review</strong> to send it to the Admin for approval. Once approved, it will go live on SkillEdu!</p>
            
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button onclick="goBack2()" class="btn" style="padding: 13px 25px; background: none; border: 1px solid var(--border-color); color: var(--dark-color);">
                    <i class="fa fa-edit"></i> Keep Editing
                </button>
                <button onclick="submitForReview()" id="publish-btn" class="btn btn-primary" style="padding: 13px 30px; font-size: 16px; font-weight: 800; background: #2ecc71; border-color: #2ecc71;">
                    <i class="fa fa-paper-plane"></i> Submit for Admin Review
                </button>
            </div>
            <div id="publish-msg" style="margin-top: 20px; font-weight: 700; font-size: 14px;"></div>
        </div>
    </div>
</div>

<script>
    let createdCourseId = null;
    let lessonCount = 0;

    // --- STEP NAVIGATION ---
    function showStep(n) {
        [1,2,3].forEach(i => document.getElementById('wizard-step-' + i).style.display = 'none');
        document.getElementById('wizard-step-' + n).style.display = 'block';
        // Update step indicator colors
        [1,2,3].forEach(i => {
            const circle = document.getElementById('step' + i + '-circle');
            const label = document.getElementById('step' + i + '-label');
            if(circle && label) {
                if(i <= n) {
                    circle.style.background = '#9b59b6';
                    circle.style.border = 'none';
                    circle.style.color = 'white';
                    label.style.color = '#9b59b6';
                } else {
                    circle.style.background = 'var(--light-gray)';
                    circle.style.border = '2px solid var(--border-color)';
                    circle.style.color = 'var(--gray-color)';
                    label.style.color = 'var(--gray-color)';
                }
            }
        });
    }

    function goBack() { showStep(1); }
    function goBack2() { showStep(2); }

    // --- STEP 1: SUBMIT BASICS ---
    async function submitBasics() {
        const btn = document.getElementById('basics-btn');
        const msg = document.getElementById('basics-msg');
        const title = document.getElementById('course-title').value.trim();
        const desc = document.getElementById('course-desc').value.trim();
        const cat = document.getElementById('course-category').value;
        const diff = document.getElementById('course-difficulty').value;
        const pricing = document.getElementById('course-pricing-type').value;
        const price = pricing === 'paid' ? (parseFloat(document.getElementById('course-price').value) || 0) : 0;

        if (!title) { showMsg(msg, 'Course title is required.', false); return; }
        if (!desc || desc.length < 30) { showMsg(msg, 'Please write a longer description (at least 30 characters).', false); return; }
        if (!cat) { showMsg(msg, 'Please select a category.', false); return; }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

        const formData = new FormData();
        formData.append('action', 'create_course');
        formData.append('title', title);
        formData.append('description', desc);
        formData.append('category_id', cat);
        formData.append('difficulty_level', diff);
        formData.append('price', price);
        const thumbFile = document.getElementById('thumbnail-file').files[0];
        if (thumbFile) formData.append('thumbnail', thumbFile);

        try {
            const res = await fetch('../../api/instructor_courses.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                createdCourseId = data.course_id;
                document.getElementById('publish-course-title').innerText = title;
                showStep(2);
            } else {
                showMsg(msg, data.message, false);
                btn.disabled = false;
                btn.innerHTML = 'Next: Build Curriculum <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>';
            }
        } catch(e) {
            showMsg(msg, 'Network error. Please try again.', false);
            btn.disabled = false;
            btn.innerHTML = 'Next: Build Curriculum <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>';
        }
    }

    // --- STEP 2: ADD LESSON ---
    function addLesson() {
        document.getElementById('no-lessons-placeholder').style.display = 'none';
        lessonCount++;
        const idx = lessonCount;
        const lesson = document.createElement('div');
        lesson.id = 'lesson-block-' + idx;
        lesson.style = 'border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; margin-bottom: 15px; background: var(--light-gray);';
        lesson.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <span style="font-weight: 800; color: var(--dark-color);">Lesson ${idx}</span>
                <button type="button" onclick="document.getElementById('lesson-block-${idx}').remove(); lessonCount--;" style="background: none; border: none; color: #e74c3c; cursor: pointer; font-size: 14px;"><i class="fa fa-trash-alt"></i> Remove</button>
            </div>
            <div style="margin-bottom: 12px;">
                <input type="text" placeholder="Lesson Title" class="lesson-title" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--dark-color); font-family: inherit;">
            </div>
            <div style="margin-bottom: 12px;">
                <select class="lesson-type" onchange="updateLessonFields(this, ${idx})" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--dark-color); font-family: inherit; appearance: auto;">
                    <option value="video">🎥 Video Lesson</option>
                    <option value="text">📝 Text / Reading</option>
                    <option value="quiz">🧪 Quiz</option>
                </select>
            </div>
            <div id="lesson-fields-${idx}">
                <input type="url" placeholder="Video URL (YouTube or direct .mp4 link)" class="lesson-url" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--dark-color); font-family: inherit;">
            </div>`;
        document.getElementById('lesson-list').appendChild(lesson);
    }

    function updateLessonFields(select, idx) {
        const container = document.getElementById('lesson-fields-' + idx);
        const type = select.value;
        if (type === 'video') {
            container.innerHTML = `<input type="url" placeholder="Video URL (YouTube or .mp4 link)" class="lesson-url" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--dark-color); font-family: inherit;">`;
        } else if (type === 'text') {
            container.innerHTML = `<textarea placeholder="Write lesson content here..." class="lesson-content" rows="5" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--dark-color); font-family: inherit; resize: vertical;"></textarea>`;
        } else {
            container.innerHTML = `<div style="padding: 15px; background: var(--bg-card); border-radius: 6px; border: 1px solid var(--border-color); color: var(--gray-color); font-size: 13px;"><i class="fa fa-info-circle"></i> Quiz questions can be added after saving the course from the "My Courses" edit page.</div>`;
        }
    }

    async function goToPublish() {
        if (!createdCourseId) { return showStep(3); }
        
        // Save all lessons
        const blocks = document.querySelectorAll('[id^="lesson-block-"]');
        const lessons = [];
        let order = 1;
        blocks.forEach(block => {
            const title = block.querySelector('.lesson-title')?.value.trim();
            const type = block.querySelector('.lesson-type')?.value;
            const url = block.querySelector('.lesson-url')?.value.trim() || '';
            const content = block.querySelector('.lesson-content')?.value.trim() || '';
            if (title) lessons.push({ title, type: type || 'video', url, content, order: order++ });
        });

        if (lessons.length > 0) {
            await fetch('../../api/instructor_courses.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'save_lessons', course_id: createdCourseId, lessons })
            });
        }
        showStep(3);
    }

    // --- STEP 3: SUBMIT FOR REVIEW ---
    async function submitForReview() {
        const btn = document.getElementById('publish-btn');
        const msg = document.getElementById('publish-msg');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';

        const res = await fetch('../../api/instructor_courses.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'submit_for_review', course_id: createdCourseId })
        });
        const data = await res.json();
        if (data.success) {
            msg.innerHTML = '<span style="color: #2ecc71;"><i class="fa fa-check-circle"></i> ' + data.message + '</span>';
            setTimeout(() => { window.location.href = 'courses.php'; }, 2200);
        } else {
            msg.innerHTML = '<span style="color:#e74c3c;">' + data.message + '</span>';
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane"></i> Submit for Admin Review';
        }
    }

    // --- HELPERS ---
    function showMsg(el, text, success) {
        el.style.display = 'block';
        el.style.background = success ? 'rgba(46,204,113,0.1)' : 'rgba(231,76,60,0.1)';
        el.style.color = success ? '#2ecc71' : '#e74c3c';
        el.style.border = '1px solid ' + (success ? '#2ecc71' : '#e74c3c');
        el.innerText = text;
    }

    function togglePriceField() {
        const t = document.getElementById('course-pricing-type').value;
        document.getElementById('price-field').style.display = t === 'paid' ? 'block' : 'none';
    }

    function previewThumbnail(input) {
        if (input.files && input.files[0]) {
            const zone = document.getElementById('thumbnail-dropzone');
            zone.innerHTML = '<img src="' + URL.createObjectURL(input.files[0]) + '" style="max-height: 180px; border-radius: 8px; object-fit: cover;">';
        }
    }
</script>

<?php include '../../includes/instructor/instructor_footer.php'; ?>
