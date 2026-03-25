<?php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$portal_type = 'student';

$root = "../../";
$page_title = 'Learning Planner';
include '../../includes/portal_header.php';
?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="font-size: 32px; font-weight: 800; color: var(--dark-color);">Learning Planner</h1>
            <button class="btn btn-primary" onclick="alert('Syncing with Google Calendar coming soon!')"><i class="fa fa-sync-alt"></i> Sync Calendar</button>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            
            <!-- Calendar Side -->
            <div style="background: var(--bg-card); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h2 id="calendar-month-year" style="font-size: 20px; color: var(--dark-color); margin: 0;">March 2026</h2>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn btn-secondary" style="padding: 5px 15px;" onclick="changeMonth(-1)"><i class="fa fa-chevron-left"></i></button>
                        <button class="btn btn-secondary" style="padding: 5px 15px;" onclick="changeMonth(1)"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; text-align: center; margin-bottom: 10px;">
                    <div style="font-weight: 700; color: var(--gray-color); font-size: 14px;">Sun</div>
                    <div style="font-weight: 700; color: var(--gray-color); font-size: 14px;">Mon</div>
                    <div style="font-weight: 700; color: var(--gray-color); font-size: 14px;">Tue</div>
                    <div style="font-weight: 700; color: var(--gray-color); font-size: 14px;">Wed</div>
                    <div style="font-weight: 700; color: var(--gray-color); font-size: 14px;">Thu</div>
                    <div style="font-weight: 700; color: var(--gray-color); font-size: 14px;">Fri</div>
                    <div style="font-weight: 700; color: var(--gray-color); font-size: 14px;">Sat</div>
                </div>

                <div id="calendar-grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px;">
                    <!-- JS will populate -->
                </div>
            </div>

            <!-- Schedule Side -->
            <div>
                <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow); margin-bottom: 25px;">
                    <h3 style="font-size: 18px; color: var(--dark-color); margin-bottom: 20px;">Schedule for <span id="selected-date-label">Today</span></h3>
                    
                    <div id="schedule-empty" style="text-align: center; padding: 30px 0; color: var(--gray-color);">
                        <i class="far fa-calendar-times" style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>No study sessions scheduled for this day.</p>
                        <button class="btn" style="margin-top: 15px; border: 1px solid #FF416C; color: #FF416C; background: transparent; font-weight: 700;" onclick="document.getElementById('add-session-modal').style.display='block'">+ Add Session</button>
                    </div>

                    <div id="schedule-list" style="display: none;">
                        <div style="display: flex; align-items: flex-start; gap: 15px; padding: 15px; border-radius: 8px; background: var(--bg-page); border-left: 4px solid #FF416C; margin-bottom: 15px;">
                            <div style="color: var(--dark-color); font-weight: 800; font-size: 14px; min-width: 60px;">10:00 AM</div>
                            <div>
                                <h4 style="font-size: 15px; margin-bottom: 5px; color: var(--dark-color);">Python Mastery</h4>
                                <p style="font-size: 13px; color: var(--gray-color); margin: 0;">Complete Section 4 Lectures</p>
                            </div>
                        </div>
                        <button class="btn" style="width: 100%; border: 1px dashed var(--gray-color); color: var(--gray-color); background: transparent; font-weight: 700;" onclick="document.getElementById('add-session-modal').style.display='block'">+ Add Another</button>
                    </div>
                </div>
                
                <div style="background: linear-gradient(135deg, var(--white) 0%, var(--border-color) 100%); padding: 30px; border-radius: 12px; color: #fff; text-align: center; box-shadow: var(--shadow);">
                    <i class="fa fa-fire" style="font-size: 40px; color: #FF416C; margin-bottom: 15px;"></i>
                    <h3 style="font-size: 20px; margin-bottom: 10px;">Maintain Your Streak!</h3>
                    <p style="font-size: 14px; opacity: 0.8; margin-bottom: 20px;">Schedule at least 15 minutes of learning every day to keep your streak alive.</p>
                </div>
            </div>

        </div>

        <!-- Add Session Modal -->
        <div id="add-session-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
            <div style="background: var(--bg-card); width: 100%; max-width: 500px; border-radius: 12px; padding: 30px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 25px;">
                    <h2 style="font-size: 22px; color: var(--dark-color); margin: 0;">Schedule Study Session</h2>
                    <button onclick="document.getElementById('add-session-modal').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--gray-color);">&times;</button>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 14px; font-weight: 700; margin-bottom: 8px; color: var(--dark-color);">Course</label>
                    <select style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
                        <option>Python for Beginners</option>
                        <option>Advanced CSS Animations</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 700; margin-bottom: 8px; color: var(--dark-color);">Time</label>
                        <input type="time" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;" value="18:30">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 700; margin-bottom: 8px; color: var(--dark-color);">Duration</label>
                        <select style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--light-gray); color: var(--dark-color); font-family: inherit;">
                            <option>15 mins</option>
                            <option>30 mins</option>
                            <option selected>1 hour</option>
                            <option>2 hours</option>
                        </select>
                    </div>
                </div>

                <button class="btn btn-primary" style="width: 100%;" onclick="saveSession()">Save to Planner</button>
            </div>
        </div>

    </div>

<style>
.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
    color: var(--dark-color);
    position: relative;
}
.calendar-day:hover:not(.empty) {
    background-color: var(--light-gray);
}
.calendar-day.active {
    background-color: var(--primary-color);
    color: white;
    box-shadow: 0 4px 10px rgba(255, 65, 108, 0.3);
}
.calendar-day.has-event::after {
    content: '';
    position: absolute;
    bottom: 5px;
    width: 6px;
    height: 6px;
    background-color: #2ecc71;
    border-radius: 50%;
}
.calendar-day.active.has-event::after {
    background-color: white;
}
.calendar-day.empty {
    cursor: default;
}
</style>

<script>
    let currentDate = new Date();
    
    function renderCalendar() {
        const grid = document.getElementById('calendar-grid');
        grid.innerHTML = '';
        
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        document.getElementById('calendar-month-year').innerText = `${monthNames[month]} ${year}`;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        // Empty cells for alignment
        for(let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.className = 'calendar-day empty';
            grid.appendChild(el);
        }
        
        const today = new Date();
        
        for(let i = 1; i <= daysInMonth; i++) {
            const el = document.createElement('div');
            el.className = 'calendar-day';
            el.innerText = i;
            
            // Mock data - put dots on 3rd, 12th, 15th
            if(i === 3 || i === 12 || i === 15) {
                el.classList.add('has-event');
            }
            
            if(year === today.getFullYear() && month === today.getMonth() && i === today.getDate()) {
                el.classList.add('active');
            }
            
            el.onclick = () => {
                document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('active'));
                el.classList.add('active');
                
                document.getElementById('selected-date-label').innerText = `${monthNames[month]} ${i}, ${year}`;
                
                if(el.classList.contains('has-event')) {
                    document.getElementById('schedule-empty').style.display = 'none';
                    document.getElementById('schedule-list').style.display = 'block';
                } else {
                    document.getElementById('schedule-empty').style.display = 'block';
                    document.getElementById('schedule-list').style.display = 'none';
                }
            };
            
            grid.appendChild(el);
        }
    }
    
    function changeMonth(delta) {
        currentDate.setMonth(currentDate.getMonth() + delta);
        renderCalendar();
    }
    
    function saveSession() {
        document.getElementById('add-session-modal').style.display = 'none';
        alert('Session saved successfully!');
        
        // Mock UI update
        const activeDay = document.querySelector('.calendar-day.active');
        if(activeDay && !activeDay.classList.contains('has-event')) {
            activeDay.classList.add('has-event');
            document.getElementById('schedule-empty').style.display = 'none';
            document.getElementById('schedule-list').style.display = 'block';
        }
    }
    
    document.addEventListener('DOMContentLoaded', renderCalendar);
</script>

<?php include '../../includes/portal_footer.php'; ?>
