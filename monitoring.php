<?php require_once 'auth/check_session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring | Exam Secure</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/monitoring-style.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="monitoring-container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <div class="header">
            <span class="title">Monitoring</span>
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
        
        <?php
        require_once 'database/db_config.php';
        
        // Fetch unique values for filters
        $owner_id = $_SESSION['unique_id'];
        
        // Get unique course codes
        $courseCodes = [];
        $sql = "SELECT DISTINCT course_code FROM created_assessments WHERE owner_id = ? AND course_code IS NOT NULL AND course_code != '' ORDER BY course_code";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $courseCodes[] = $row['course_code'];
        }
        
        // Get unique year & program combinations
        $yearPrograms = [];
        $sql = "SELECT DISTINCT year_course FROM created_assessments WHERE owner_id = ? AND year_course IS NOT NULL AND year_course != '' ORDER BY year_course";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $yearPrograms[] = $row['year_course'];
        }
        
        // Get unique school years
        $schoolYears = [];
        $sql = "SELECT DISTINCT school_year FROM created_assessments WHERE owner_id = ? AND school_year IS NOT NULL AND school_year != '' ORDER BY school_year DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $schoolYears[] = $row['school_year'];
        }
        ?>
        
        <div class="monitoring-list-title">Active Assessments</div>
        <div class="monitoring-filters-row">
            <select class="monitoring-filter-dropdown" id="monitoringStatusFilter">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="closed">Closed</option>
            </select>
            <select class="monitoring-filter-dropdown" id="monitoringCourseCodeFilter">
                <option value="">All Course Codes</option>
                <?php foreach ($courseCodes as $code): ?>
                    <option value="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($code) ?></option>
                <?php endforeach; ?>
            </select>
            <select class="monitoring-filter-dropdown" id="monitoringYearProgramFilter">
                <option value="">All Year & Program</option>
                <?php foreach ($yearPrograms as $yp): ?>
                    <option value="<?= htmlspecialchars($yp) ?>"><?= htmlspecialchars($yp) ?></option>
                <?php endforeach; ?>
            </select>
            <select class="monitoring-filter-dropdown" id="monitoringSchoolYearFilter">
                <option value="">All School Years</option>
                <?php foreach ($schoolYears as $sy): ?>
                    <option value="<?= htmlspecialchars($sy) ?>"><?= htmlspecialchars($sy) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="monitoring-search-bar-wrapper">
                <svg class="monitoring-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b3c6e0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>
                <input type="text" class="monitoring-search-bar" id="monitoringSearchBar" placeholder="Search assessments...">
            </div>
            <button class="monitoring-refresh-btn" title="Refresh" onclick="refreshMonitoringFilters()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M23 4v6h-6"/>
                    <path d="M1 20v-6h6"/>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                </svg>
            </button>
        </div>
        <div class="monitoring-list">
            <?php
            require_once 'database/db_config.php';
            $allAssessments = [];
            $owner_id = $_SESSION['unique_id'];
            $sql = "SELECT 
                        ca.unique_id,
                        ca.title, 
                        ca.schedule, 
                        ca.year_course, 
                        ca.course_code, 
                        ca.status, 
                        ca.access_code, 
                        ca.school_year,
                        (
                            COALESCE((SELECT COUNT(*) FROM multiple_choice_questions WHERE assessment_id = ca.unique_id), 0) +
                            COALESCE((SELECT COUNT(*) FROM identification_questions WHERE assessment_id = ca.unique_id), 0) +
                            COALESCE((SELECT COUNT(*) FROM true_false_questions WHERE assessment_id = ca.unique_id), 0)
                        ) as total_questions,
                        COALESCE((SELECT COUNT(*) FROM assessment_sessions WHERE assessment_id = ca.unique_id AND status = 'completed'), 0) as total_submissions
                    FROM created_assessments ca 
                    WHERE ca.owner_id = ? 
                    ORDER BY (ca.status = 'active') DESC, ca.schedule DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $owner_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $allAssessments[] = $row;
                }
            }
            
            if (empty($allAssessments)): ?>
                <div class="no-assessments-message">
                    <div class="no-assessments-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#b3c6e0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10,9 9,9 8,9"/>
                        </svg>
                    </div>
                    <h3>No Assessments Found</h3>
                    <p>You haven't created any assessments yet. Click "Create Assessment" to get started!</p>
                </div>
            <?php else: ?>
            <?php foreach ($allAssessments as $assessment): ?>
                <div class="monitoring-card" 
                     data-course-code="<?= htmlspecialchars($assessment['course_code']) ?>"
                     data-year-program="<?= htmlspecialchars($assessment['year_course']) ?>"
                     data-school-year="<?= htmlspecialchars($assessment['school_year']) ?>"
                     data-status="<?= htmlspecialchars($assessment['status']) ?>"
                     data-title="<?= htmlspecialchars(strtolower($assessment['title'])) ?>">
                    <div class="monitoring-card-header">
                        <span class="monitoring-card-title"><?= htmlspecialchars($assessment['title']) ?></span>
                        <span class="monitoring-card-date"><?= date('m/d/Y', strtotime($assessment['schedule'])) ?></span>
                    </div>
                    <div class="monitoring-card-details">
                        <?= htmlspecialchars($assessment['year_course']) ?> &bull; <?= htmlspecialchars($assessment['course_code']) ?> &bull; <?= $assessment['total_questions'] ?> Questions &bull; <?= $assessment['total_submissions'] ?> Submissions
                    </div>
                    <div class="monitoring-card-badges">
                        <span class="monitoring-status-badge <?= $assessment['status'] === 'active' ? 'active' : 'closed' ?>">
                            <?= ucfirst($assessment['status']) ?>
                        </span>
                        <span class="monitoring-code-badge">CODE: <?= htmlspecialchars($assessment['access_code']) ?></span>
                        <a class="monitoring-details-link" href="monitoring_assessment.php?id=<?= htmlspecialchars($assessment['unique_id']) ?>">Monitor Assessment <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg></a>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="js/sidebar-active.js"></script>
<script>
// Filter functionality for monitoring
function filterMonitoring() {
    const courseCodeFilter = document.getElementById('monitoringCourseCodeFilter').value;
    const yearProgramFilter = document.getElementById('monitoringYearProgramFilter').value;
    const schoolYearFilter = document.getElementById('monitoringSchoolYearFilter').value;
    const statusFilter = document.getElementById('monitoringStatusFilter').value;
    const searchBar = document.getElementById('monitoringSearchBar').value.toLowerCase();
    
    const monitoringCards = document.querySelectorAll('.monitoring-card');
    
    monitoringCards.forEach(card => {
        const courseCode = card.dataset.courseCode;
        const yearProgram = card.dataset.yearProgram;
        const schoolYear = card.dataset.schoolYear;
        const title = card.dataset.title;
        const status = card.dataset.status;
        
        let showCard = true;
        
        // Apply course code filter
        if (courseCodeFilter && courseCode !== courseCodeFilter) {
            showCard = false;
        }
        
        // Apply year & program filter
        if (yearProgramFilter && yearProgram !== yearProgramFilter) {
            showCard = false;
        }
        
        // Apply school year filter
        if (schoolYearFilter && schoolYear !== schoolYearFilter) {
            showCard = false;
        }
        // Apply status filter
        if (statusFilter && status !== statusFilter) {
            showCard = false;
        }
        
        // Apply search filter
        if (searchBar && !title.includes(searchBar)) {
            showCard = false;
        }
        
        // Show/hide card
        card.style.display = showCard ? 'block' : 'none';
    });
}

// Add event listeners for monitoring filters
document.getElementById('monitoringCourseCodeFilter').addEventListener('change', filterMonitoring);
document.getElementById('monitoringYearProgramFilter').addEventListener('change', filterMonitoring);
document.getElementById('monitoringSchoolYearFilter').addEventListener('change', filterMonitoring);
document.getElementById('monitoringStatusFilter').addEventListener('change', filterMonitoring);
document.getElementById('monitoringSearchBar').addEventListener('input', filterMonitoring);

// Refresh filters function for monitoring
function refreshMonitoringFilters() {
    // Clear all filter values
    document.getElementById('monitoringCourseCodeFilter').value = '';
    document.getElementById('monitoringYearProgramFilter').value = '';
    document.getElementById('monitoringSchoolYearFilter').value = '';
    document.getElementById('monitoringStatusFilter').value = '';
    document.getElementById('monitoringSearchBar').value = '';
    
    // Show all monitoring cards
    const monitoringCards = document.querySelectorAll('.monitoring-card');
    monitoringCards.forEach(card => {
        card.style.display = 'block';
    });
    
    // Add visual feedback
    const refreshBtn = document.querySelector('.monitoring-refresh-btn');
    refreshBtn.style.transform = 'rotate(360deg)';
    refreshBtn.style.transition = 'transform 0.5s ease';
    
    setTimeout(() => {
        refreshBtn.style.transform = 'rotate(0deg)';
    }, 500);
    
    console.log('Monitoring filters refreshed - all assessments shown');
}

// Simple profile tooltip toggle
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
