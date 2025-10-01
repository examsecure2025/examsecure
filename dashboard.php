<?php
require_once 'auth/check_session.php';
require_once 'database/db_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Exam Secure</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-style.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
    <div class="header">
            <span class="title">Dashboard</span>
            
            <div style="display: flex; align-items: center; gap: 16px;">
            <button class="create-btn" onclick="window.location.href='create_assessment.php'">Create Assessment</button>
                <span class="profile-icon" id="profileIcon" style="cursor:pointer; position:relative;">
                    <i class="bi bi-person-circle" style="font-size:48px; color:#fff;"></i>
                </span>
                <div id="profileMenu" style="display:none; position:absolute; top:64px; right:16px; background:#11224a; border:none; border-radius:0.75rem; padding:0.5rem; box-shadow:0 10px 30px rgba(0,0,0,0.35); z-index:10000; min-width: 220px;">
                    <span style="position:absolute; top:-6px; right:24px; width:12px; height:12px; background:#11224a; transform: rotate(45deg);"></span>
                    <div style="padding:0.5rem 0.75rem; color:#b3c6e0; font-size:0.9rem; border-bottom:1px solid #25477a; margin-bottom:0.25rem;">Profile</div>
                    <button type="button" onclick="window.location.href='account_settings.php'" style="background:transparent; color:#fff; border:none; border-radius:0.5rem; padding:0.6rem 0.75rem; font-weight:600; cursor:pointer; width:100%; text-align:left; display:flex; align-items:center; gap:0.5rem;"
                        onmouseover="this.style.background='#17305c'" onmouseout="this.style.background='transparent'">
                        <i class="bi bi-gear" style="font-size:1rem;"></i>
                        <span>Account Settings</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="recent-assessments-section">
            <div class="recent-assessments-header">
                
                <span class="recent-title">Recent Assessments</span>
                <span class="legend">
                    <span class="legend-dot finished"></span> Finished
                    <span class="legend-dot answering"></span> Answering
                    <span class="legend-dot cheating"></span> Flagged as Cheating
                </span>
            </div>
            <div class="recent-assessments" id="recentAssessments">
                <div class="loading-container">
                    <div class="loading-card">
                        <div class="loading-spinner"></div>
                        <div class="loading-text">Loading assessments...</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cheating-alerts-section">
            <div class="cheating-alerts-header">
                <span class="cheating-title">Cheating Alerts</span>
            </div>
            <div class="cheating-alerts" id="cheatingAlerts">
                <div class="loading-container">
                    <div class="loading-card">
                        <div class="loading-spinner"></div>
                        <div class="loading-text">Loading alerts...</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-tables">
            <div class="dashboard-table">
                <div class="table-title">Recent Submissions <span style="font-weight: 400; font-size: 0.95rem; color: #b3c6e0; float: right; cursor: pointer;">Show details &rarr;</span></div>
                <div id="recentSubmissions">
                    <div class="loading-container">
                        <div class="loading-card">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Loading submissions...</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboard-table">
                <div class="table-title">Top Scores</div>
                <div id="topScores">
                    <div class="loading-container">
                        <div class="loading-card">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Loading scores...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="js/sidebar-active.js"></script>
<script src="js/dashboard-functions.js"></script>
<script>
document.addEventListener('click', function(e) {
    const icon = document.getElementById('profileIcon');
    const menu = document.getElementById('profileMenu');
    if (!icon || !menu) return;
    if (icon.contains(e.target)) {
        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
    } else if (!menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});
</script>
</body>
</html>
