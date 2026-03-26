/**
 * assets/js/student_stats.js
 * 
 * Learning progress and achievement engine for the Student Portal.
 */

class StudentStats {
    constructor(config = {}) {
        this.baseUrl = config.baseUrl || '../../';
        this.charts = {};
        this.data = null;

        this.init();
    }

    async init() {
        console.log('🎓 Student Achievement Engine Initialized');
        await this.loadData();
        this.renderOverview();
        this.renderCharts();
        this.renderGoalCanvas(); // New raw Canvas logic
        this.renderCourseProgress();

        // Polling for updates (achievement notifications)
        this.startPolling();
    }

    async loadData() {
        try {
            const response = await fetch(`${this.baseUrl}api/student_stats.php`);
            const result = await response.json();

            if (result.success) {
                this.data = result;
            }
        } catch (error) {
            console.error('Failed to load student analytics:', error);
        }
    }

    renderOverview() {
        if (!this.data) return;

        const { stats } = this.data;
        this.updateEl('total-enrolled-val', stats.total_enrolled);
        this.updateEl('completed-courses-val', stats.completed);
        this.updateEl('certificates-val', stats.certificates);
        this.updateEl('learning-points-val', stats.points);
    }

    updateEl(id, val) {
        const el = document.getElementById(id);
        if (el) el.innerText = val;
    }

    renderCharts() {
        const ctx = document.getElementById('learningActivityChart');
        if (!ctx) return;

        const labels = this.data.weekly_learning.map(d => d.day);
        const values = this.data.weekly_learning.map(d => d.minutes);

        this.charts.activity = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Learning Minutes',
                    data: values,
                    backgroundColor: '#e74c3c',
                    borderRadius: 6,
                    hoverBackgroundColor: '#c0392b'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false }, ticks: { font: { weight: '600' } } },
                    x: { grid: { display: false }, ticks: { font: { weight: '600' } } }
                }
            }
        });
    }

    renderCourseProgress() {
        const container = document.getElementById('student-course-list');
        if (!container || !this.data.course_progress.length) return;

        container.innerHTML = this.data.course_progress.map(c => `
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-weight: 700; font-size: 14px;">${c.title}</span>
                    <span style="font-weight: 800; color: var(--primary-color); font-size: 13px;">${c.progress_percent}%</span>
                </div>
                <div style="height: 8px; background: var(--border-color); border-radius: 4px; overflow: hidden;">
                    <div style="width: ${c.progress_percent}%; height: 100%; background: var(--primary-gradient); transition: width 1s ease-out;"></div>
                </div>
            </div>
        `).join('');
    }

    renderGoalCanvas() {
        const canvas = document.getElementById('goalCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = 50;
        const progress = 0.75; // 75% goal completion

        // Draw Background Circle
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI, false);
        ctx.lineWidth = 10;
        ctx.strokeStyle = '#f0f0f0';
        ctx.stroke();

        // Draw Progress Arc
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, -Math.PI / 2, (-Math.PI / 2) + (2 * Math.PI * progress), false);
        ctx.lineWidth = 10;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#e74c3c';
        ctx.stroke();

        // Draw Text
        ctx.font = 'bold 20px Inter, sans-serif';
        ctx.fillStyle = document.documentElement.getAttribute('data-theme') === 'dark' ? '#fff' : '#2c3e50';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('75%', centerX, centerY);
    }

    startPolling() {
        setInterval(async () => {
            const oldPoints = this.data.stats.points;
            await this.loadData();
            if (this.data.stats.points > oldPoints) {
                this.showAchievementToast(this.data.stats.points - oldPoints);
            }
        }, 30000);
    }

    showAchievementToast(pts) {
        if (typeof showToast === 'function') {
            showToast('Achievement!', `You just earned ${pts} learning points! 🌟`, 'success');
        }
    }
}

// Global initialization
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('learningActivityChart')) {
        window.StudentAnalytics = new StudentStats({
            baseUrl: window.SkillEduConfig.baseUrl
        });
    }
});
