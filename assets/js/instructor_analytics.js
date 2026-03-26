/**
 * assets/js/instructor_analytics.js
 * 
 * High-fidelity analytics engine for the Instructor Dashboard.
 * Handles data fetching, chart rendering, and real-time UI updates.
 */

class InstructorAnalytics {
    constructor(config = {}) {
        this.baseUrl = config.baseUrl || '../../';
        this.charts = {};
        this.refreshInterval = config.refreshInterval || 60000;
        this.data = null;

        this.init();
    }

    async init() {
        console.log('📊 Instructor Analytics Engine Initialized');
        await this.loadData();
        this.renderStats();
        this.renderCharts();
        this.startPolling();

        // Listen for theme changes to refresh charts
        window.addEventListener('storage', (e) => {
            if (e.key === 'skilledu_theme') this.refreshCharts();
        });

        // BroadcastChannel for cross-tab updates
        this.channel = new BroadcastChannel('instructor_sync');
        this.channel.onmessage = (msg) => {
            if (msg.data.type === 'REFRESH_ANALYTICS') this.loadData(true);
        };
    }

    async loadData(silent = false) {
        if (!silent) this.showSkeletons();

        try {
            const response = await fetch(`${this.baseUrl}api/instructor_analytics.php?action=dashboard_stats`);
            const result = await response.json();

            if (result.success) {
                this.data = result;
                if (silent) {
                    this.renderStats();
                    this.updateCharts();
                }
            }
        } catch (error) {
            console.error('Failed to load analytics data:', error);
        } finally {
            if (!silent) this.hideSkeletons();
        }
    }

    showSkeletons() {
        const containers = document.querySelectorAll('.stat-value');
        containers.forEach(c => c.classList.add('skeleton-pulse'));
    }

    hideSkeletons() {
        const containers = document.querySelectorAll('.stat-value');
        containers.forEach(c => c.classList.remove('skeleton-pulse'));
    }

    renderStats() {
        if (!this.data) return;

        const { stats } = this.data;
        this.animateValue('total-students-val', stats.total_students);
        this.animateValue('published-courses-val', stats.published_courses);
        this.animateValue('avg-completion-val', stats.avg_completion, '%');
    }

    animateValue(id, value, suffix = '') {
        const el = document.getElementById(id);
        if (!el) return;

        let start = 0;
        const duration = 1000;
        const startTime = performance.now();

        const update = (now) => {
            const pct = Math.min((now - startTime) / duration, 1);
            const current = Math.floor(pct * value);
            el.innerText = current + suffix;

            if (pct < 1) requestAnimationFrame(update);
            else el.innerText = value + suffix;
        };
        requestAnimationFrame(update);
    }

    renderCharts() {
        this.renderEnrollmentTrend();
        this.renderDistributionChart();
    }

    renderEnrollmentTrend() {
        const ctx = document.getElementById('enrollmentChart');
        if (!ctx) return;

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const labels = this.data.trends.map(t => t.label);
        const values = this.data.trends.map(t => t.value);

        this.charts.trends = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'New Students',
                    data: values,
                    borderColor: '#9b59b6',
                    backgroundColor: 'rgba(155, 89, 182, 0.1)',
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#9b59b6',
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: this.getChartOptions('Enrollments')
        });
    }

    renderDistributionChart() {
        const ctx = document.getElementById('distributionChart');
        if (!ctx) return;

        const labels = this.data.distribution.map(d => d.title.substring(0, 15) + '...');
        const values = this.data.distribution.map(d => d.students);

        this.charts.distribution = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [
                        '#9b59b6', '#3498db', '#2ecc71', '#f1c40f', '#e67e22'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } }
                }
            }
        });
    }

    updateCharts() {
        if (this.charts.trends) {
            this.charts.trends.data.datasets[0].data = this.data.trends.map(t => t.value);
            this.charts.trends.update();
        }
    }

    refreshCharts() {
        Object.values(this.charts).forEach(chart => {
            const options = this.getChartOptions();
            chart.options.scales.y.grid.color = options.scales.y.grid.color;
            chart.options.scales.y.ticks.color = options.scales.y.ticks.color;
            chart.options.scales.x.ticks.color = options.scales.x.ticks.color;
            chart.update();
        });
    }

    getChartOptions(title = '') {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
        const textColor = isDark ? '#a0a0a0' : '#7f8c8d';

        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#2c3e50' : '#fff',
                    titleColor: isDark ? '#fff' : '#2c3e50',
                    bodyColor: isDark ? '#fff' : '#2c3e50',
                    borderColor: gridColor,
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor, drawBorder: false },
                    ticks: { color: textColor, padding: 10, font: { weight: '600' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: textColor, padding: 10, font: { weight: '600' } }
                }
            }
        };
    }

    startPolling() {
        setInterval(() => this.loadData(true), this.refreshInterval);
    }
}

// Global initialization
document.addEventListener('DOMContentLoaded', () => {
    window.Analytics = new InstructorAnalytics({
        baseUrl: '../../'
    });
});
