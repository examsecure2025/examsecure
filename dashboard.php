<?php
require_once 'auth/check_session.php';
require_once 'database/db_config.php'; // adjust path if needed

// Fetch the 5 most recent assessments for the logged-in user only
$recentAssessments = [];
$owner_id = $_SESSION['unique_id'];
$sql = "SELECT title, course_code, year_course FROM created_assessments WHERE owner_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentAssessments[] = $row;
    }
}
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
            <div class="recent-assessments">
                <?php foreach ($recentAssessments as $assessment): ?>
                    <div class="assessment-card">
                        <div class="assessment-card-inner">
                            <div class="assessment-info-block">
                                <div class="assessment-title"><?= htmlspecialchars($assessment['title']) ?></div>
                                <div class="assessment-divider"></div>
                                <div class="assessment-info-row">
                                    <div class="assessment-info-label"><?= htmlspecialchars($assessment['course_code']) ?></div>
                                    <div class="assessment-info-label"><?= htmlspecialchars($assessment['year_course']) ?></div>
                                </div>
                            </div>
                            <div class="assessment-status-block">
                                <div class="status-row"><span class="status-dot finished"></span><span class="status-count">0</span></div>
                                <div class="status-row"><span class="status-dot answering"></span><span class="status-count">0</span></div>
                                <div class="status-row"><span class="status-dot cheating"></span><span class="status-count">0</span></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="cheating-alerts-section">
            <div class="cheating-alerts-header">
                <span class="cheating-title">Cheating Alerts</span>
            </div>
            <div class="cheating-alerts">
                <div class="cheating-alert">
                    <span class="alert-icon">&#9888;</span>
                    <div class="alert-details">
                        <strong>Doe, John L.</strong><br>
                        III BSIT C &nbsp;·&nbsp; Midterm Exam &nbsp;·&nbsp; ELEC1 &nbsp;·&nbsp; 05/14/2025 - 11:23 PM
                    </div>
                    <div class="alert-action">
                        <a href="#">Show full details &rarr;</a>
                    </div>
                </div>
                <div class="cheating-alert">
                    <span class="alert-icon">&#9888;</span>
                    <div class="alert-details">
                        <strong>Downey, Sarah O.</strong><br>
                        II BSIT A &nbsp;·&nbsp; Quiz #2 &nbsp;·&nbsp; SAD101 &nbsp;·&nbsp; 05/14/2025 - 1:03 PM
                    </div>
                    <div class="alert-action">
                        <a href="#">Show full details &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-tables">
            <div class="dashboard-table">
                <div class="table-title">Recent Submissions <span style="font-weight: 400; font-size: 0.95rem; color: #b3c6e0; float: right; cursor: pointer;">Show details &rarr;</span></div>
                <div class="table-row"><span>Swiss, Lee S.<br><span style="font-size:0.95em; color:#b3c6e0;">III BSIT C · ELEC1</span></span> <span>Midterm Exam</span></div>
                <div class="table-row"><span>Esperas, Danny R.<br><span style="font-size:0.95em; color:#b3c6e0;">II BSIT A · SAD101</span></span> <span>Quiz #2</span></div>
                <div class="table-row"><span>Paiso, Kevin C.<br><span style="font-size:0.95em; color:#b3c6e0;">II BSIT A · SAD101</span></span> <span>Quiz #2</span></div>
            </div>
            <div class="dashboard-table">
                <div class="table-title">Top Scores</div>
                <div class="table-row"><span>Tandoc, Don Lee A.<br><span style="font-size:0.95em; color:#b3c6e0;">III BSIT C · Midterm Exam · ELEC1</span></span> <span class="score">56/60</span></div>
                <div class="table-row"><span>Soriano, Janjo E.<br><span style="font-size:0.95em; color:#b3c6e0;">II BSIT A · Quiz #2 · SAD101</span></span> <span class="score">17/20</span></div>
                <div class="table-row"><span>Paiso, Kevin C.<br><span style="font-size:0.95em; color:#b3c6e0;">II BSIT A · Quiz #2 · SAD101</span></span> <span class="score">16/20</span></div>
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
