<?php require_once 'auth/check_session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Monitoring | Exam Secure</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/monitoring-assessment-style.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="monitoring-assessment-container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <?php
        require_once 'database/db_config.php';
        
        if (!isset($_GET['id'])) {
            header('Location: monitoring.php');
            exit();
        }
        
        $assessment_id = $_GET['id'];
        $owner_id = $_SESSION['unique_id'];
        
        // Fetch assessment details and verify ownership
        $sql = "SELECT * FROM created_assessments WHERE unique_id = ? AND owner_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $assessment_id, $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            header('Location: monitoring.php');
            exit();
        }
        
        $assessment = $result->fetch_assoc();
        
        // Get unique sections for this assessment from student sessions
        $sections_sql = "SELECT DISTINCT year_section FROM assessment_sessions WHERE assessment_id = ? AND year_section IS NOT NULL AND year_section != '' ORDER BY year_section";
        $sections_stmt = $conn->prepare($sections_sql);
        $sections_stmt->bind_param("s", $assessment_id);
        $sections_stmt->execute();
        $sections_result = $sections_stmt->get_result();
        
        $sections = [];
        while ($row = $sections_result->fetch_assoc()) {
            $sections[] = $row['year_section'];
        }
        ?>
        
        <div class="header">
            <div class="header-left">
                <button class="back-button" onclick="window.location.href='monitoring.php'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back to Monitoring
                </button>
            </div>
            <div class="header-right">
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
        
        <!-- Assessment Information -->
        <div class="assessment-info-section">
            <div class="assessment-info">
                <h1 class="assessment-title"><?= htmlspecialchars($assessment['title']) ?></h1>
                <div class="assessment-details">
                    <span class="year-course"><?= htmlspecialchars($assessment['year_course']) ?></span>
                    <span class="course-code"><?= htmlspecialchars($assessment['course_code']) ?></span>
                    <span class="access-code">Access Code: <?= htmlspecialchars($assessment['access_code']) ?></span>
                </div>
            </div>
            <div class="assessment-status">
                <div class="status-badge active">
                    <div class="status-dot"></div>
                    <span>Active</span>
                </div>
            </div>
        </div>
        
        <!-- Quick Assessment Overview -->
        <div class="assessment-overview" id="assessmentOverview" style="display: none;">
            <div class="overview-stats">
                <div class="stat-item">
                    <span class="stat-label">Total Students:</span>
                    <span class="stat-value" id="totalStudents">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Answering:</span>
                    <span class="stat-value" id="answeringCount">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Finished:</span>
                    <span class="stat-value" id="finishedCount">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Flagged as cheating:</span>
                    <span class="stat-value" id="flaggedCount">0</span>
                </div>
            </div>
        </div>
        
        
        <!-- Filters -->
        <div class="filters-row">
            <div class="filter-group">
                <label for="sectionFilter">Section:</label>
                <select id="sectionFilter" class="filter-dropdown">
                    <option value="">All Sections</option>
                    <?php foreach ($sections as $section): ?>
                        <option value="<?= htmlspecialchars($section) ?>"><?= htmlspecialchars($section) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="sortFilter">Sort by:</label>
                <select id="sortFilter" class="filter-dropdown">
                    <option value="name">Name</option>
                    <option value="status">Status</option>
                    <option value="warnings">Warnings</option>
                    <option value="time">Time Started</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="orderFilter">Order:</label>
                <select id="orderFilter" class="filter-dropdown">
                    <option value="asc">Asc</option>
                    <option value="desc">Desc</option>
                </select>
            </div>
            <div class="search-group">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b3c6e0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"/>
                    <line x1="16.5" y1="16.5" x2="21" y2="21"/>
                </svg>
                <input type="text" id="searchStudent" class="search-input" placeholder="Search student">
            </div>
            <button class="refresh-btn" onclick="refreshData()" title="Refresh">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M23 4v6h-6"/>
                    <path d="M1 20v-6h6"/>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                </svg>
            </button>
        </div>
        
        <!-- Loading Animation -->
        <div class="loading-container" id="loadingContainer">
            <div class="loading-card">
                <div class="loading-icon">
                    <div class="loading-spinner"></div>
                    <div class="loading-pulse"></div>
                </div>
                <div class="loading-content">
                    <h3 class="loading-title">Loading Assessment Data</h3>
                    <p class="loading-subtitle">Fetching student information and monitoring data...</p>
                    <div class="loading-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
        
       
        <div class="students-grid" id="studentsGrid" style="display: none;">
            
        </div>
    </main>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Cheating Evidence Review</h2>
            <button class="close-btn" onclick="closeReviewModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="evidence-section">
                <div class="evidence-header">
                    <select id="evidenceFilter" class="evidence-filter">
                        <option value="all">All Evidence</option>
                        <option value="TAB_SWITCH">Tab Switching</option>
                        <option value="FACE_LEFT">Face Left</option>
                        <option value="FACE_RIGHT">Face Right</option>
                    </select>
                </div>
                <div class="carousel-container">
                    <button class="carousel-btn prev" onclick="previousEvidence()">&#8249;</button>
                    <div class="carousel-content">
                        <div id="evidenceCarousel" class="evidence-carousel">
                            <!-- Evidence items will be loaded here -->
                        </div>
                    </div>
                    <button class="carousel-btn next" onclick="nextEvidence()">&#8250;</button>
                </div>
                <div class="carousel-indicators" id="carouselIndicators">
                    <!-- Indicators will be generated here -->
                </div>
            </div>
            <div class="student-details-section">
                <h3>Student Details</h3>
                <div id="studentDetails">
                    <!-- Student details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Enlargement Modal -->
<div id="imageModal" class="image-modal">
    <div class="image-modal-content">
        <div class="image-modal-header">
            <h3 id="imageModalTitle">Evidence Screenshot</h3>
            <button class="image-close-btn" onclick="closeImageModal()">&times;</button>
        </div>
        <div class="image-modal-body">
            <img id="enlargedImage" src="" alt="Enlarged Evidence Screenshot" />
        </div>
        <div class="image-modal-footer">
            <button class="close-image-btn" onclick="closeImageModal()">Close</button>
        </div>
    </div>
</div>

<script src="js/sidebar-active.js"></script>
<script>
let assessmentId = '<?= $assessment_id ?>';
let refreshInterval;

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    loadStudentData();
    
    // Set up auto-refresh every 3 seconds for better real-time updates
    refreshInterval = setInterval(refreshStudentData, 3000);
    
    // Update cheating flags every 5 seconds for more real-time flagging
    const flagUpdateInterval = setInterval(updateCheatingFlags, 5000);
    
    // Set up event listeners
    document.getElementById('sectionFilter').addEventListener('change', filterStudents);
    document.getElementById('sortFilter').addEventListener('change', sortStudents);
    document.getElementById('searchStudent').addEventListener('input', filterStudents);
    
    // Profile menu toggle
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
});

// Load student monitoring data
async function loadStudentData() {
    try {
        const response = await fetch(`phpfiles/get_monitoring_data.php?assessment_id=${assessmentId}`);
        const data = await response.json();
        
        if (data.success) {
            displayStudents(data.students, false);
            updateOverviewStats(data.students);
            
            // Hide loading and show content
            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('assessmentOverview').style.display = 'block';
            document.getElementById('studentsGrid').style.display = 'grid';
        } else {
            console.error('Error loading monitoring data:', data.message);
            // Hide loading even on error
            document.getElementById('loadingContainer').style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading monitoring data:', error);
        // Hide loading even on error
        document.getElementById('loadingContainer').style.display = 'none';
    }
}

// Refresh student data without recreating cards (preserves warning messages)
async function refreshStudentData() {
    try {
        const response = await fetch(`phpfiles/get_monitoring_data.php?assessment_id=${assessmentId}`);
        const data = await response.json();
        
        if (data.success) {
            displayStudents(data.students, true); // Pass true for refresh mode
        } else {
            console.error('Error refreshing monitoring data:', data.message);
        }
    } catch (error) {
        console.error('Error refreshing monitoring data:', error);
    }
}

// Display students in the grid
function displayStudents(students, isRefresh = false) {
    const grid = document.getElementById('studentsGrid');
    
    if (students.length === 0) {
        grid.innerHTML = '<div class="no-students">No students found for this assessment.</div>';
        return;
    }
    
    // If this is a refresh, preserve existing warning messages
    if (isRefresh) {
        updateExistingCards(students);
        return;
    }
    
    // Initial load - create all cards
    grid.innerHTML = '';
   
    const sortedStudents = students.sort((a, b) => {
        const aIsCheating = (a.total_warnings || 0) > 3;
        const bIsCheating = (b.total_warnings || 0) > 3;
        
        if (aIsCheating && !bIsCheating) return -1; 
        if (!aIsCheating && bIsCheating) return 1;  
        return 0; 
    });
    
    sortedStudents.forEach(student => {
        const studentCard = createStudentCard(student);
        grid.appendChild(studentCard);
    });
}

// Update existing cards without recreating them (preserves warning messages)
function updateExistingCards(students) {
    const grid = document.getElementById('studentsGrid');
    const cards = document.querySelectorAll('.student-card');
    const studentMap = new Map();
    const existingSessionIds = new Set();
    
    // Create a map of students by session_id for quick lookup
    students.forEach(student => {
        studentMap.set(student.session_id, student);
    });
    
    // Track existing session IDs
    cards.forEach(card => {
        const sessionId = card.dataset.sessionId;
        if (sessionId) {
            existingSessionIds.add(sessionId);
            
            if (studentMap.has(sessionId)) {
                const student = studentMap.get(sessionId);
                // Update only the data that changes, not the warning messages
                updateCardData(card, student);
            }
        }
    });
    
    // Add new students who just started
    const newStudents = students.filter(student => !existingSessionIds.has(student.session_id));
    if (newStudents.length > 0) {
        // Sort new students (cheating students first)
        const sortedNewStudents = newStudents.sort((a, b) => {
            const aIsCheating = (a.total_warnings || 0) > 3;
            const bIsCheating = (b.total_warnings || 0) > 3;
            
            if (aIsCheating && !bIsCheating) return -1; 
            if (!aIsCheating && bIsCheating) return 1;  
            return 0; 
        });
        
        // Add new student cards
        sortedNewStudents.forEach(student => {
            const studentCard = createStudentCard(student);
            grid.appendChild(studentCard);
        });
    }
    
    // Remove students who are no longer in the assessment (optional - uncomment if needed)
    // const currentSessionIds = new Set(students.map(s => s.session_id));
    // cards.forEach(card => {
    //     const sessionId = card.dataset.sessionId;
    //     if (sessionId && !currentSessionIds.has(sessionId)) {
    //         card.remove();
    //     }
    // });
    
    // Update overview stats
    updateOverviewStats(students);
}

// Update card data without affecting warning messages
function updateCardData(card, student) {
    const previousWarnings = parseInt(card.dataset.warnings) || 0;
    const currentWarnings = student.total_warnings || 0;
    
    // Check if student just crossed the cheating threshold
    const justFlagged = (previousWarnings <= 3 && currentWarnings > 3);
    const justUnflagged = (previousWarnings > 3 && currentWarnings <= 3);
    
    // Update status classes
    card.classList.remove('status-flagged', 'status-finished', 'status-answering');
    
    if (student.total_warnings > 3) {
        card.classList.add('status-flagged');
        // If student is also finished, add finished class for green background
        if (student.status === 'completed') {
            card.classList.add('status-finished');
        }
        
        // Add visual feedback for newly flagged students
        if (justFlagged) {
            card.style.animation = 'pulse-red 1s ease-in-out 3';
            setTimeout(() => {
                card.style.animation = '';
            }, 3000);
            
            // Show notification
            showFlaggedNotification(student.student_name);
        }
    } else if (student.status === 'completed') {
        card.classList.add('status-finished');
    } else if (student.status === 'ongoing') {
        card.classList.add('status-answering');
    }
    
    // Update status messages
    const statusContainer = card.querySelector('.status-container');
    if (statusContainer) {
        let statusMessages = [];
        
        // Primary status first
        if (student.status === 'completed') {
            statusMessages.push('Finished');
        } else if (student.status === 'ongoing') {
            statusMessages.push('Answering');
        }
        
        // Add flagged status if applicable
        if (student.total_warnings > 3) {
            statusMessages.push('Flagged as Cheating');
        }
        
        const statusMessageHTML = statusMessages.map(msg => {
            let className = 'status-message';
            if (msg === 'Answering') className += ' status-answering-badge';
            if (msg === 'Finished') className += ' status-finished-badge';
            if (msg === 'Flagged as Cheating') className += ' status-flagged-badge';
            return `<div class="${className}">${msg}</div>`;
        }).join('');
        statusContainer.innerHTML = statusMessageHTML;
    }
    
    // Update all detail items
    const detailItems = card.querySelectorAll('.detail-item');
    detailItems.forEach(item => {
        const label = item.querySelector('.detail-label');
        const value = item.querySelector('.detail-value');
        
        if (label && value) {
            const labelText = label.textContent.trim();
            
            if (labelText === 'Section:') {
                value.textContent = student.year_section || 'N/A';
            } else if (labelText === 'Time Started:') {
                value.textContent = formatTime(student.started_at);
            } else if (labelText === 'Time Finished:') {
                if (student.completed_at) {
                    value.textContent = formatTime(student.completed_at);
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            } else if (labelText === 'Score:') {
                if (student.status === 'completed') {
                    value.textContent = student.score || 0;
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            } else if (labelText === 'Warnings:') {
                if (student.total_warnings > 3) {
                    // Hide warnings count for flagged students
                    item.style.display = 'none';
                } else {
                    // Show warnings count for non-flagged students
                    item.style.display = 'flex';
                    value.textContent = student.total_warnings || 0;
                }
            }
        }
    });
    
    // Add Time Finished and Score fields if student just completed
    if (student.status === 'completed' && student.completed_at) {
        const studentDetails = card.querySelector('.student-details');
        if (studentDetails) {
            // Check if Time Finished field already exists
            let timeFinishedExists = false;
            let scoreExists = false;
            
            detailItems.forEach(item => {
                const label = item.querySelector('.detail-label');
                if (label) {
                    if (label.textContent.trim() === 'Time Finished:') timeFinishedExists = true;
                    if (label.textContent.trim() === 'Score:') scoreExists = true;
                }
            });
            
            // Add Time Finished field if it doesn't exist
            if (!timeFinishedExists) {
                const timeFinishedItem = document.createElement('div');
                timeFinishedItem.className = 'detail-item';
                timeFinishedItem.innerHTML = `
                    <span class="detail-label">Time Finished:</span>
                    <span class="detail-value">${formatTime(student.completed_at)}</span>
                `;
                studentDetails.appendChild(timeFinishedItem);
            }
            
            // Add Score field if it doesn't exist
            if (!scoreExists) {
                const scoreItem = document.createElement('div');
                scoreItem.className = 'detail-item';
                scoreItem.innerHTML = `
                    <span class="detail-label">Score:</span>
                    <span class="detail-value">${student.score || 0}</span>
                `;
                studentDetails.appendChild(scoreItem);
            }
        }
    }
    
    // Update dataset for filtering
    card.dataset.warnings = student.total_warnings || 0;
    card.dataset.status = student.status;
    card.dataset.section = student.year_section || '';
    card.dataset.startedAt = student.started_at || '';
    
    // Update cheating student class
    if (student.total_warnings > 3) {
        card.classList.add('cheating-student');
    } else {
        card.classList.remove('cheating-student');
    }
    
    // Update or remove review button
    const actionsDiv = card.querySelector('.student-actions');
    if (actionsDiv) {
        const existingBtn = actionsDiv.querySelector('.review-btn');
        if (student.total_warnings > 3) {
            if (!existingBtn) {
                actionsDiv.innerHTML = `
                    <button class="review-btn" onclick="reviewStudent('${student.session_id}')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 6-8 6 8 6 8-4 8-6 8-6-8-6-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        Review
                    </button>
                `;
            }
        } else {
            if (existingBtn) {
                actionsDiv.innerHTML = '';
            }
        }
    }
}

function createStudentCard(student) {
    const card = document.createElement('div');
    card.className = 'student-card';
    
    // Add status-based background color and classes
    if (student.total_warnings > 3) {
        card.classList.add('cheating-student', 'status-flagged');
        // If student is also finished, add finished class for green background
        if (student.status === 'completed') {
            card.classList.add('status-finished');
        }
    } else if (student.status === 'completed') {
        card.classList.add('status-finished');
    } else if (student.status === 'ongoing') {
        card.classList.add('status-answering');
    }
    
    card.dataset.studentName = student.student_name.toLowerCase();
    card.dataset.section = student.year_section || '';
    card.dataset.status = student.status;
    card.dataset.warnings = student.total_warnings || 0;
    card.dataset.sessionId = student.session_id;
    card.dataset.startedAt = student.started_at || '';
    
    const statusClass = getStatusClass(student.status, student.cheating_flag);
    const warningMessage = getWarningMessage(student);
    
    // Get status messages - show primary status first, then flagged status
    let statusMessages = [];
    
    // Primary status first
    if (student.status === 'completed') {
        statusMessages.push('Finished');
    } else if (student.status === 'ongoing') {
        statusMessages.push('Answering');
    }
    
    // Add flagged status if applicable
    if (student.total_warnings > 3) {
        statusMessages.push('Flagged as Cheating');
    }
    
    const statusMessageHTML = statusMessages.map(msg => {
        let className = 'status-message';
        if (msg === 'Answering') className += ' status-answering-badge';
        if (msg === 'Finished') className += ' status-finished-badge';
        if (msg === 'Flagged as Cheating') className += ' status-flagged-badge';
        return `<div class="${className}">${msg}</div>`;
    }).join('');
    
    card.innerHTML = `
        <div class="student-header">
            <h3 class="student-name">${student.student_name}</h3>
        </div>
        <div class="student-details">
            <div class="detail-item">
                <span class="detail-label">Section:</span>
                <span class="detail-value">${student.year_section || 'N/A'}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Time Started:</span>
                <span class="detail-value">${formatTime(student.started_at)}</span>
            </div>
            ${student.completed_at ? `
                <div class="detail-item">
                    <span class="detail-label">Time Finished:</span>
                    <span class="detail-value">${formatTime(student.completed_at)}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Score:</span>
                    <span class="detail-value">${student.score || 0}</span>
                </div>
            ` : ''}
            ${student.total_warnings <= 3 ? `
                <div class="detail-item">
                    <span class="detail-label">Warnings:</span>
                    <span class="detail-value">${student.total_warnings || 0}</span>
                </div>
            ` : ''}
            ${warningMessage ? `
                <div class="warning-message" id="warning-${student.session_id}">${warningMessage}</div>
            ` : ''}
        </div>
        <div class="student-actions">
            ${student.total_warnings > 3 ? `
                <button class="review-btn" onclick="reviewStudent('${student.session_id}')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 6-8 6 8 6 8-4 8-6 8-6-8-6-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Review
                </button>
            ` : ''}
            <div class="status-container">
                ${statusMessageHTML}
            </div>
        </div>
    `;
    
    // Auto-hide warning message after 5 seconds
    if (warningMessage) {
        setTimeout(() => {
            const warningElement = document.getElementById(`warning-${student.session_id}`);
            if (warningElement) {
                warningElement.style.opacity = '0';
                warningElement.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    if (warningElement.parentNode) {
                        warningElement.parentNode.removeChild(warningElement);
                    }
                }, 300); // Wait for fade animation to complete
            }
        }, 5000);
    }
    
    return card;
}


function getStatusClass(status, cheatingFlag) {
    if (cheatingFlag) return 'flagged';
    if (status === 'completed') return 'finished';
    if (status === 'ongoing') return 'answering';
    return 'answering';
}


function getWarningMessage(student) {
    // Don't show warning messages for students who are already flagged as cheating
    if (!student.recent_warning || (student.total_warnings || 0) > 3) return '';
    
    const warnings = {
        'TAB_SWITCH': 'A Switching tabs',
        'FACE_LEFT': 'A Looking away',
        'FACE_RIGHT': 'A Looking away',
        'SUSPICIOUS': 'A Suspicious activity',
        'SCREENSHOT': 'A Screenshot detected'
    };
    
    return warnings[student.recent_warning] || 'A Suspicious activity';
}


function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
}


function updateOverviewStats(students) {
    const total = students.length;
    const answering = students.filter(s => s.status === 'ongoing' && s.total_warnings <= 3).length;
    const finished = students.filter(s => s.status === 'completed').length;
    const flagged = students.filter(s => s.total_warnings > 3).length;
    
    document.getElementById('totalStudents').textContent = total;
    document.getElementById('answeringCount').textContent = answering;
    document.getElementById('finishedCount').textContent = finished;
    document.getElementById('flaggedCount').textContent = flagged;
}

// Show notification when a student gets flagged
function showFlaggedNotification(studentName) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'flagged-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">⚠️</div>
            <div class="notification-text">
                <strong>${studentName}</strong> has been flagged for cheating!
            </div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Update cheating flags in database
async function updateCheatingFlags() {
    try {
        const response = await fetch('phpfiles/update_cheating_flag.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                assessment_id: '<?= $assessment_id ?>'
            })
        });
        
        const data = await response.json();
        if (data.success) {
            // If any students were flagged or unflagged, refresh the data immediately
            if (data.flagged_students > 0 || data.unflagged_students > 0) {
                console.log(`Updated cheating flags: ${data.flagged_students} flagged, ${data.unflagged_students} unflagged`);
                // Trigger immediate refresh to show the updated flags
                refreshStudentData();
            }
        }
    } catch (error) {
        console.error('Error updating cheating flags:', error);
    }
}


function filterStudents() {
    const sectionFilter = document.getElementById('sectionFilter').value;
    const searchTerm = document.getElementById('searchStudent').value.toLowerCase();
    const cards = document.querySelectorAll('.student-card');
    
    cards.forEach(card => {
        const section = card.dataset.section;
        const name = card.dataset.studentName;
        
        let show = true;
        
        if (sectionFilter && section !== sectionFilter) {
            show = false;
        }
        
        if (searchTerm && !name.includes(searchTerm)) {
            show = false;
        }
        
        card.style.display = show ? 'block' : 'none';
    });
    
    
    const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
    const grid = document.getElementById('studentsGrid');
    
    
    visibleCards.sort((a, b) => {
        const aIsCheating = (parseInt(a.dataset.warnings) || 0) > 3;
        const bIsCheating = (parseInt(b.dataset.warnings) || 0) > 3;
        
        if (aIsCheating && !bIsCheating) return -1;
        if (!aIsCheating && bIsCheating) return 1;
        return 0;
    });
    
    
    visibleCards.forEach(card => {
        grid.appendChild(card);
    });
}


function sortStudents() {
    const sortBy = document.getElementById('sortFilter').value;
    const order = (document.getElementById('orderFilter')?.value || 'asc').toLowerCase();
    const grid = document.getElementById('studentsGrid');
    const cards = Array.from(grid.querySelectorAll('.student-card'));
    
    cards.sort((a, b) => {
        
        const aIsCheating = (parseInt(a.dataset.warnings) || 0) > 3;
        const bIsCheating = (parseInt(b.dataset.warnings) || 0) > 3;
        
        if (aIsCheating && !bIsCheating) return -1; 
        if (!aIsCheating && bIsCheating) return 1;  
        
        
        switch (sortBy) {
            case 'name':
                return order === 'asc'
                    ? a.dataset.studentName.localeCompare(b.dataset.studentName)
                    : b.dataset.studentName.localeCompare(a.dataset.studentName);
            case 'status':
                return order === 'asc'
                    ? a.dataset.status.localeCompare(b.dataset.status)
                    : b.dataset.status.localeCompare(a.dataset.status);
            case 'warnings':
                return order === 'asc'
                    ? (parseInt(a.dataset.warnings) - parseInt(b.dataset.warnings))
                    : (parseInt(b.dataset.warnings) - parseInt(a.dataset.warnings));
            case 'time':
                const at = a.dataset.startedAt ? new Date(a.dataset.startedAt).getTime() : 0;
                const bt = b.dataset.startedAt ? new Date(b.dataset.startedAt).getTime() : 0;
                return order === 'asc' ? (at - bt) : (bt - at);
            default:
                return 0;
        }
    });
    
    
    cards.forEach(card => grid.appendChild(card));
    
    // Resort immediately when order changes
    const orderEl = document.getElementById('orderFilter');
    orderEl?.addEventListener('change', () => {
        sortStudents();
    });
}


function refreshData() {
    refreshStudentData();
    
   
    const refreshBtn = document.querySelector('.refresh-btn');
    refreshBtn.style.transform = 'rotate(360deg)';
    refreshBtn.style.transition = 'transform 0.5s ease';
    
    setTimeout(() => {
        refreshBtn.style.transform = 'rotate(0deg)';
    }, 500);
}


let currentEvidence = [];
let currentEvidenceIndex = 0;
let filteredEvidence = [];

async function reviewStudent(sessionId) {
    try {
       
        document.getElementById('reviewModal').style.display = 'block';
        
      
        document.getElementById('evidenceCarousel').innerHTML = '<div class="no-evidence">Loading evidence...</div>';
        document.getElementById('studentDetails').innerHTML = '<div>Loading student details...</div>';
        
        
        const response = await fetch(`phpfiles/get_cheating_evidence.php?session_id=${sessionId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('API Response:', data); 
        
        if (data.success) {
            currentEvidence = data.evidence;
            filteredEvidence = [...currentEvidence];
            currentEvidenceIndex = 0;
            
           
            displayStudentDetails(data.student);
            
          
            displayEvidence();
            updateCarouselIndicators();
        } else {
            console.error('API Error:', data.message);
            document.getElementById('evidenceCarousel').innerHTML = '<div class="no-evidence">Error: ' + data.message + '</div>';
            document.getElementById('studentDetails').innerHTML = '<div>Error loading student details</div>';
        }
    } catch (error) {
        console.error('Fetch Error:', error);
        document.getElementById('evidenceCarousel').innerHTML = '<div class="no-evidence">Error loading evidence: ' + error.message + '</div>';
        document.getElementById('studentDetails').innerHTML = '<div>Error loading student details</div>';
    }
}


function displayStudentDetails(student) {
    const studentDetails = document.getElementById('studentDetails');
    studentDetails.innerHTML = `
        <div class="detail-row">
            <span class="detail-label">Name:</span>
            <span class="detail-value">${student.student_name}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Section:</span>
            <span class="detail-value">${student.year_section || 'N/A'}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Started:</span>
            <span class="detail-value">${formatDateTime(student.started_at)}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="detail-value">${student.status}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Total Warnings:</span>
            <span class="detail-value">${student.total_warnings}</span>
        </div>
    `;
}


function displayEvidence() {
    const carousel = document.getElementById('evidenceCarousel');
    
    if (filteredEvidence.length === 0) {
        carousel.innerHTML = '<div class="no-evidence">No evidence found for the selected filter.</div>';
        return;
    }
    
    const currentItem = filteredEvidence[currentEvidenceIndex];
    const eventTypeLabels = {
        'TAB_SWITCH': 'Tab Switching',
        'FACE_LEFT': 'Face Left',
        'FACE_RIGHT': 'Face Right'
    };
    
    const severityColors = {
        'LOW': '#4CAF50',
        'MEDIUM': '#FF9800',
        'HIGH': '#F44336'
    };
    
    carousel.innerHTML = `
        <div class="evidence-item">
            <div class="evidence-header-info">
                <h4>${eventTypeLabels[currentItem.event_type] || currentItem.event_type}</h4>
                <span class="severity-badge" style="background-color: ${severityColors[currentItem.severity]}">
                    ${currentItem.severity}
                </span>
            </div>
            <div class="evidence-time">
                <strong>Detected:</strong> ${formatDateTime(currentItem.event_time)}
            </div>
            ${currentItem.screenshot_url ? `
                <div class="evidence-screenshot">
                    <img src="${currentItem.screenshot_url}" alt="Evidence Screenshot" 
                         onclick="openImageModal('${currentItem.screenshot_url}', '${eventTypeLabels[currentItem.event_type] || currentItem.event_type}')" 
                         class="clickable-image" />
                </div>
            ` : `
                <div class="no-screenshot">
                    <p>No screenshot available for this event</p>
                </div>
            `}
        </div>
    `;
}


function updateCarouselIndicators() {
    const indicators = document.getElementById('carouselIndicators');
    indicators.innerHTML = '';
    
    for (let i = 0; i < filteredEvidence.length; i++) {
        const indicator = document.createElement('span');
        indicator.className = `indicator ${i === currentEvidenceIndex ? 'active' : ''}`;
        indicator.onclick = () => goToEvidence(i);
        indicators.appendChild(indicator);
    }
}


function goToEvidence(index) {
    currentEvidenceIndex = index;
    displayEvidence();
    updateCarouselIndicators();
}


function previousEvidence() {
    if (filteredEvidence.length === 0) return;
    currentEvidenceIndex = (currentEvidenceIndex - 1 + filteredEvidence.length) % filteredEvidence.length;
    displayEvidence();
    updateCarouselIndicators();
}


function nextEvidence() {
    if (filteredEvidence.length === 0) return;
    currentEvidenceIndex = (currentEvidenceIndex + 1) % filteredEvidence.length;
    displayEvidence();
    updateCarouselIndicators();
}


function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
    currentEvidence = [];
    filteredEvidence = [];
    currentEvidenceIndex = 0;
}


document.addEventListener('DOMContentLoaded', function() {
    const evidenceFilter = document.getElementById('evidenceFilter');
    if (evidenceFilter) {
        evidenceFilter.addEventListener('change', function() {
            const filterValue = this.value;
            if (filterValue === 'all') {
                filteredEvidence = [...currentEvidence];
            } else {
                filteredEvidence = currentEvidence.filter(item => item.event_type === filterValue);
            }
            currentEvidenceIndex = 0;
            displayEvidence();
            updateCarouselIndicators();
        });
    }
});


function formatDateTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
}


function openImageModal(imageSrc, eventType) {
    const imageModal = document.getElementById('imageModal');
    const enlargedImage = document.getElementById('enlargedImage');
    const imageModalTitle = document.getElementById('imageModalTitle');
    
    enlargedImage.src = imageSrc;
    imageModalTitle.textContent = `${eventType} - Evidence Screenshot`;
    imageModal.style.display = 'block';
}

function closeImageModal() {
    const imageModal = document.getElementById('imageModal');
    imageModal.style.display = 'none';
}


document.addEventListener('DOMContentLoaded', function() {
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('click', function(e) {
            if (e.target === imageModal) {
                closeImageModal();
            }
        });
    }
});


window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>
</body>
</html>
