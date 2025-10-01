document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    
    // Refresh data every 30 seconds
    setInterval(loadDashboardData, 30000);
});

async function loadDashboardData() {
    try {
        const response = await fetch('phpfiles/get_dashboard_data.php');
        const data = await response.json();
        
        if (data.success) {
            displayRecentAssessments(data.recentAssessments);
            displayCheatingAlerts(data.cheatingAlerts);
            displayRecentSubmissions(data.recentSubmissions);
            displayTopScores(data.topScores);
        } else {
            console.error('Error loading dashboard data:', data.message);
        }
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    }
}

function displayRecentAssessments(assessments) {
    const container = document.getElementById('recentAssessments');
    
    if (assessments.length === 0) {
        container.innerHTML = '<div class="no-data">No assessments found</div>';
        return;
    }
    
    container.innerHTML = assessments.map(assessment => `
        <div class="assessment-card" onclick="goToMonitoring('${assessment.unique_id}')">
            <div class="assessment-card-inner">
                <div class="assessment-info-block">
                    <div class="assessment-title">${escapeHtml(assessment.title)}</div>
                    <div class="assessment-divider"></div>
                    <div class="assessment-info-row">
                        <div class="assessment-info-label">${escapeHtml(assessment.course_code)}</div>
                        <div class="assessment-info-label">${escapeHtml(assessment.year_course)}</div>
                    </div>
                </div>
                <div class="assessment-status-block">
                    <div class="status-row">
                        <span class="status-dot finished"></span>
                        <span class="status-count">${assessment.finished_count || 0}</span>
                    </div>
                    <div class="status-row">
                        <span class="status-dot answering"></span>
                        <span class="status-count">${assessment.answering_count || 0}</span>
                    </div>
                    <div class="status-row">
                        <span class="status-dot cheating"></span>
                        <span class="status-count">${assessment.cheating_count || 0}</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function displayCheatingAlerts(alerts) {
    const container = document.getElementById('cheatingAlerts');
    
    if (alerts.length === 0) {
        container.innerHTML = '<div class="no-data">No cheating alerts in the last 24 hours</div>';
        return;
    }
    
    container.innerHTML = alerts.map(alert => `
        <div class="cheating-alert">
            <span class="alert-icon">⚠</span>
            <div class="alert-details">
                <strong>${escapeHtml(alert.student_name)}</strong><br>
                ${escapeHtml(alert.year_section)} &nbsp;·&nbsp; ${escapeHtml(alert.assessment_title)} &nbsp;·&nbsp; ${escapeHtml(alert.course_code)} &nbsp;·&nbsp; ${formatDateTime(alert.started_at)}
            </div>
            <div class="alert-action">
                <a href="monitoring_assessment.php?id=${alert.assessment_id}" onclick="event.stopPropagation()">Show full details →</a>
            </div>
        </div>
    `).join('');
}

function displayRecentSubmissions(submissions) {
    const container = document.getElementById('recentSubmissions');
    
    if (submissions.length === 0) {
        container.innerHTML = '<div class="no-data">No recent submissions</div>';
        return;
    }
    
    container.innerHTML = submissions.map(submission => `
        <div class="table-row">
            <span>
                ${escapeHtml(submission.student_name)}<br>
                <span style="font-size:0.95em; color:#b3c6e0;">${escapeHtml(submission.year_section)} · ${escapeHtml(submission.course_code)}</span>
            </span>
            <span>${escapeHtml(submission.assessment_title)}</span>
        </div>
    `).join('');
}

function displayTopScores(scores) {
    const container = document.getElementById('topScores');
    
    if (scores.length === 0) {
        container.innerHTML = '<div class="no-data">No completed assessments</div>';
        return;
    }
    
    container.innerHTML = scores.map(score => `
        <div class="table-row">
            <span>
                ${escapeHtml(score.student_name)}<br>
                <span style="font-size:0.95em; color:#b3c6e0;">${escapeHtml(score.year_section)} · ${escapeHtml(score.assessment_title)} · ${escapeHtml(score.course_code)}</span>
            </span>
            <span class="score">${score.total_score}/${score.total_possible}</span>
        </div>
    `).join('');
}

function goToMonitoring(assessmentId) {
    window.location.href = `monitoring_assessment.php?id=${assessmentId}`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDateTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleDateString('en-US', {
        month: '2-digit',
        day: '2-digit',
        year: 'numeric'
    }) + ' - ' + date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}
