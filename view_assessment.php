<?php require_once 'auth/check_session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assessment | Exam Secure</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/create_assessment-style.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <style>
        .back-button {
            background: #17305c;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .back-button:hover {
            background: #25477a;
        }
        .assessment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .assessment-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
        }
        .assessment-code {
            background: #19d3ff;
            color: #002147;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .summary-stats {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            color: #b3c6e0;
            font-size: 1.1rem;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .save-btn {
            background: #ffe600;
            color: #002147;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
        }
        .save-btn:hover {
            background: #e6d200;
        }
        .delete-btn {
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
        }
        .delete-btn:hover {
            background: #c0392b;
        }
        .questions-summary {
            background: #11224a;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .question-type-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #17305c;
            border-radius: 0.5rem;
        }
        .question-count {
            color: #ffe600;
            font-weight: 700;
        }
        .edit-questions-btn {
            background: #25477a;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            cursor: pointer;
        }
        .edit-questions-btn:hover {
            background: #3fa9f5;
        }
        .readonly-field {
            background: #0a1a36 !important;
            color: #b3c6e0 !important;
            cursor: not-allowed;
        }
        
        .status-indicator {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-indicator.active {
            background: #1ed760;
            color: #00194a;
        }
        
        .status-indicator.closed {
            background: #b3c6e0;
            color: #00194a;
        }
        
        .highlight-number {
            color: #ffe600;
            font-weight: 700;
            font-size: 1.2em;
        }
        
        .choice-item.hidden {
            display: none !important;
        }

        .mcq-choice-box-input::active {
            border-left: 3px solid #ffe600;
        }
        
        /* Focus styles for MCQ inputs */
        .mcq-question-input:focus {
            border-bottom: 3px solid #ffe600 !important;
        }
        .mcq-choice-input:focus {
            border-left: 3px solid #ffe600 !important;
        }

        /* Error highlight styles */
        .error-left {
            border-left: 3px solid #e74c3c !important;
        }
        .error-bottom {
            border-bottom: 3px solid #e74c3c !important;
        }
        .error-all {
            border: 2px solid #e74c3c !important;
        }
    </style>
</head>
<body>
<div class="create-assessment-container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <?php
        require_once 'database/db_config.php';
        
        if (!isset($_GET['id'])) {
            header('Location: assessments.php');
            exit();
        }
        
        $assessment_id = $_GET['id'];
        $owner_id = $_SESSION['unique_id'];
        
        // Fetch assessment details
        $sql = "SELECT * FROM created_assessments WHERE unique_id = ? AND owner_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $assessment_id, $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            header('Location: assessments.php');
            exit();
        }
        
        $assessment = $result->fetch_assoc();
        
        
        if (!isset($assessment['year_course'])) {
            $assessment['year_course'] = '';
        }
        
        
        $mcq_count = 0;
        $id_count = 0;
        $tf_count = 0;
        $mcq_points = 0;
        $id_points = 0;
        $tf_points = 0;
        

        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(points), 0) as total_points FROM multiple_choice_questions WHERE assessment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $assessment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            $mcq_count = $row['count'];
            $mcq_points = $row['total_points'];
        }
        
        
        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(points), 0) as total_points FROM identification_questions WHERE assessment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $assessment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            $id_count = $row['count'];
            $id_points = $row['total_points'];
        }
        
       
        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(points), 0) as total_points FROM true_false_questions WHERE assessment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $assessment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            $tf_count = $row['count'];
            $tf_points = $row['total_points'];
        }
        
        $total_questions = $mcq_count + $id_count + $tf_count;
        $total_points = $mcq_points + $id_points + $tf_points;
        ?>
        
        <button class="back-button" onclick="window.location.href='assessments.php'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Assessments
        </button>
        
        <div class="assessment-header">
            <h1 class="assessment-title"><?= htmlspecialchars($assessment['title']) ?></h1>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="assessment-code">CODE: <?= htmlspecialchars($assessment['access_code']) ?></div>
                <div class="status-indicator <?= $assessment['status'] === 'active' ? 'active' : 'closed' ?>">
                    <?= ucfirst($assessment['status']) ?>
                </div>
            </div>
        </div>
        
        <div class="summary-stats">
            <span>Total Questions: <span class="highlight-number"><?= $total_questions ?></span></span>
            <span>Total Points: <span class="highlight-number"><?= $total_points ?></span></span>
        </div>
        
        <form class="assessment-details-form" id="editAssessmentForm">
            <input type="hidden" id="assessment_id" value="<?= htmlspecialchars($assessment['unique_id']) ?>">
            
            <section class="details-section">
                <div class="section-title">Details</div>
                <div class="details-grid">
                    <div class="form-group">
                        <label for="title">Title of the test</label>
                        <input type="text" id="title" value="<?= htmlspecialchars($assessment['title']) ?>" placeholder="Enter title">
                    </div>
                    <div class="form-group">
                        <label for="year_course">Year & Course</label>
                        <input type="text" id="year_course" value="<?= htmlspecialchars($assessment['year_course'] ?? '') ?>" placeholder="Enter Y & C">
                        <span class="input-hint">Format : III BSIT</span>
                    </div>
                    <div class="form-group">
                        <label for="section">Add Section</label>
                        <div class="section-input-row">
                            <input type="text" id="section" value="<?= htmlspecialchars($assessment['sections']) ?>" placeholder="Add section">
                        </div>
                        <span class="input-hint">Format : A,B,D</span>
                    </div>
                    <div class="form-group">
                        <label for="course_code">Course Code</label>
                        <input type="text" id="course_code" value="<?= htmlspecialchars($assessment['course_code']) ?>" placeholder="Enter course code">
                    </div>
                    <div class="form-group">
                        <label for="timer">Timer</label>
                        <select id="timer">
                            <option value="0" <?= $assessment['timer'] == 0 ? 'selected' : '' ?>>No time limit</option>
                            <option value="30" <?= $assessment['timer'] == 30 ? 'selected' : '' ?>>30 minutes</option>
                            <option value="60" <?= $assessment['timer'] == 60 ? 'selected' : '' ?>>60 minutes</option>
                            <option value="90" <?= $assessment['timer'] == 90 ? 'selected' : '' ?>>90 minutes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status">
                            <option value="active" <?= $assessment['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="closed" <?= $assessment['status'] == 'closed' ? 'selected' : '' ?>>Close</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="school_year">School Year</label>
                        <input type="text" id="school_year" value="<?= htmlspecialchars($assessment['school_year']) ?>" placeholder="Enter S.Y">
                        <span class="input-hint">Format : 2024-2025</span>
                    </div>
                    <div class="form-group schedule-group">
                        <label>Schedule Assessment</label>
                        <div class="datetime-row">
                            <input type="date" id="schedule_date" value="<?= date('Y-m-d', strtotime($assessment['schedule'])) ?>">
                            <select id="schedule_time">
                                <?php
                                $schedule_time = date('h:i A', strtotime($assessment['schedule']));
                                $time_options = [
                                    '08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
                                    '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM'
                                ];
                                foreach ($time_options as $time) {
                                    $selected = ($time == $schedule_time) ? 'selected' : '';
                                    echo "<option value=\"$time\" $selected>$time</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group schedule-group">
                        <label>Automatically Close Assessment</label>
                        <div class="datetime-row">
                            <input type="date" id="close_date" value="<?= date('Y-m-d', strtotime($assessment['closing_time'])) ?>">
                            <select id="close_time">
                                <?php
                                $close_time = date('h:i A', strtotime($assessment['closing_time']));
                                foreach ($time_options as $time) {
                                    $selected = ($time == $close_time) ? 'selected' : '';
                                    echo "<option value=\"$time\" $selected>$time</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </section>
            
            <section class="questions-section">
                <div class="section-title">Questions</div>
                <div class="questions-summary">
                    <div class="question-type-summary">
                        <span>Multiple Choice Questions</span>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span class="question-count"><?= $mcq_count ?> questions (<?= $mcq_points ?> pts)</span>
                            <button type="button" class="edit-questions-btn" onclick="editQuestions('mcq')">Edit Questions</button>
                        </div>
                    </div>
                    <div class="question-type-summary">
                        <span>Identification Questions</span>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span class="question-count"><?= $id_count ?> questions (<?= $id_points ?> pts)</span>
                            <button type="button" class="edit-questions-btn" onclick="editQuestions('id')">Edit Questions</button>
                        </div>
                    </div>
                    <div class="question-type-summary">
                        <span>True or False Questions</span>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span class="question-count"><?= $tf_count ?> questions (<?= $tf_points ?> pts)</span>
                            <button type="button" class="edit-questions-btn" onclick="editQuestions('tf')">Edit Questions</button>
                        </div>
                    </div>
                </div>
            </section>
        </form>
        
        <div class="action-buttons">
            <button type="button" id="mainSaveBtn" class="save-btn" onclick="saveAssessment()">Save Changes</button>
            <button type="button" class="delete-btn" style="background:#6c757d;" onclick="window.location.href='assessments.php'">Cancel</button>
            <button type="button" class="delete-btn" onclick="deleteAssessment()">Delete Assessment</button>
        </div>
        
    
        <div id="questionsModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(10,26,54,0.98); z-index:9999; overflow:auto;">
            <div class="modal-content" style="max-width:1200px; margin:2rem auto; background:#0a1a36; border-radius:1.5rem; padding:2rem; box-shadow:0 0.25rem 1.5rem 0 rgba(0,0,0,0.18); position:relative;">
                <button id="closeQuestionsModal" style="position:absolute; top:1.5rem; right:2rem; background:none; border:none; color:#fff; font-size:2rem; cursor:pointer; transition: color 0.2s;" onmouseover="this.style.color='#ffe600'" onmouseout="this.style.color='#fff'">&times;</button>
                <div id="questionsModalContent" style="color:#fff; padding-right: 2rem;">
              
                </div>
            </div>
        </div>
        
        <button class="floating-btn" title="Scroll up">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#002147" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"/></svg>
        </button>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/sidebar-active.js"></script>

<script>
let currentQuestionType = '';
let pendingQuestionChanges = {
    mcq: null,
    id: null,
    tf: null
};

function editQuestions(type) {
    currentQuestionType = type;
    const assessmentId = document.getElementById('assessment_id').value;
    
  
    document.getElementById('questionsModal').style.display = 'block';
    
  
    loadQuestions(type, assessmentId);
}

function loadQuestions(type, assessmentId) {
    const modalContent = document.getElementById('questionsModalContent');
    
    
    modalContent.innerHTML = '<div style="text-align: center; padding: 2rem;">Loading questions...</div>';
    
    
    Promise.all([
        fetch(`phpfiles/get_questions.php?type=${type}&assessment_id=${assessmentId}`),
        fetch(`phpfiles/get_assessment_settings.php?assessment_id=${assessmentId}`)
    ])
    .then(responses => Promise.all(responses.map(r => r.json())))
    .then(([questionsData, settingsData]) => {
   
        const actualSettings = settingsData && settingsData.settings ? settingsData.settings : {};
        console.log('Actual settings to use:', actualSettings);
        displayQuestions(type, questionsData, actualSettings);
    })
    .catch(error => {
        console.error('Error loading questions:', error);
        modalContent.innerHTML = '<div style="text-align: center; padding: 2rem; color: #e74c3c;">Error loading questions</div>';
    });
}

function displayQuestions(type, questions, settings) {
    const modalContent = document.getElementById('questionsModalContent');
    let html = '';
    

    console.log('Display Questions - Type:', type);
    console.log('Settings received:', settings);
    console.log('Settings types:', {
        shuffle_mcq: typeof settings?.shuffle_mcq,
        shuffle_identification: typeof settings?.shuffle_identification,
        ai_check_identification: typeof settings?.ai_check_identification,
        shuffle_true_false: typeof settings?.shuffle_true_false
    });
    
    // Merge queued unsaved drafts first, then server questions
    const queued = pendingQuestionChanges[type]?.questions || [];
    const drafts = queued.filter(q => !q.question_id);
    const mergedQuestions = [...drafts, ...questions];
    const mergedSettings = pendingQuestionChanges[type]?.shuffle_settings ? pendingQuestionChanges[type].shuffle_settings : settings;
    
    if (type === 'mcq') {
        html = generateMCQHTML(mergedQuestions, mergedSettings);
    } else if (type === 'id') {
        html = generateIDHTML(mergedQuestions, mergedSettings);
    } else if (type === 'tf') {
        html = generateTFHTML(mergedQuestions, mergedSettings);
    }
    
    modalContent.innerHTML = html;
    
    
    if (type === 'mcq') {
        addMCQChoicesEventListeners();
    }
}

function generateMCQHTML(questions, settings) {
    let html = `
        <h2 style="margin-bottom: 2rem;">Edit Multiple Choice Questions</h2>
        
        <!-- Settings Section -->
        <div style="background: #17305c; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; ">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <label style="color: #b3c6e0; font-weight: 600;">Shuffle Questions:</label>
                                                    <select id="mcq-shuffle" style="background: #25477a; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; color: #fff; font-size: 1rem; outline: none;">
                        <option value="1" ${settings && settings.shuffle_mcq === 1 ? 'selected' : ''}>On</option>
                        <option value="0" ${settings && settings.shuffle_mcq === 0 ? 'selected' : ''}>Off</option>
                    </select>
                </div>
                <div style="color: #b3c6e0; font-weight: 600;">
                    Questions made: <span style="color: #ffe600;">${questions.length}</span>
                </div>
            </div>
        </div>
        
        <div id="mcq-questions-container">
    `;
    
    if (questions.length === 0) {
        html += '<p style="text-align: center; color: #b3c6e0;">No questions found</p>';
    } else {
                    questions.forEach((question, index) => {
                html += `
                    <div class="mcq-question-item" data-question-id="${question.question_id}" style="background: #17305c; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <!-- Question Header -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <h3 style="color: #fff; font-size: 1.5rem; font-weight: 700; margin: 0;">Question #${index + 1} ${!question.question_id ? '<span style="background:#777; color:#fff; font-size:0.8rem; padding:0.2rem 0.5rem; border-radius:0.3rem; margin-left:0.5rem;">Unsaved</span>' : ''}</h3>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <label style="color: #b3c6e0; font-weight: 600;">Point/s:</label>
                                <select class="mcq-points" style="background: #25477a; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; color: #fff; font-size: 1rem; outline: none;">
                                    <option value="1" ${question.points == 1 ? 'selected' : ''}>1</option>
                                    <option value="2" ${question.points == 2 ? 'selected' : ''}>2</option>
                                    <option value="3" ${question.points == 3 ? 'selected' : ''}>3</option>
                                    <option value="4" ${question.points == 4 ? 'selected' : ''}>4</option>
                                    <option value="5" ${question.points == 5 ? 'selected' : ''}>5</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Question Input -->
                        <div style="margin-bottom: 1.5rem;">
                            <input type="text" class="mcq-question-input" value="${question.question_text}" placeholder="Enter Question" style="width: 100%; background: #25477a; border: none; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1.1rem; outline: none;">
                        </div>
                        
                        <!-- Choices Section -->
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <label style="color: #b3c6e0; font-weight: 600; font-size: 1.1rem;">Choices:</label>
                                <select class="mcq-choices-count" onchange="handleChoicesCountChange(this)" style="background: #25477a; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; color: #fff; font-size: 1rem; outline: none;">
                                    <option value="3" ${getChoicesCount(question) == 3 ? 'selected' : ''}>3</option>
                                    <option value="4" ${getChoicesCount(question) == 4 ? 'selected' : ''}>4</option>
                                    <option value="5" ${getChoicesCount(question) == 5 ? 'selected' : ''}>5</option>
                                </select>
                            </div>
                            
                            <div class="mcq-choices-list" style="display: flex; flex-direction: column; gap: 1rem;">
                                <div class="choice-item" data-choice="A" style="display: flex; align-items: center; gap: 1rem;">
                                    <span class="mcq-choice-box-input" style="background: #25477a; color: #ffe600; width: 2.2rem; height: 2.2rem; border-radius: 0.4rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">A</span>
                                    <input type="text" class="mcq-choice-input" value="${question.option_a || ''}" placeholder="Enter Choice A" style="flex: 1; border-left: 3px solid #11224a; background: #25477a; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none;">
                                </div>
                                <div class="choice-item" data-choice="B" style="display: flex; align-items: center; gap: 1rem;">
                                    <span class="mcq-choice-box-input" style="background: #25477a; color: #ffe600; width: 2.2rem; height: 2.2rem; border-radius: 0.4rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">B</span>
                                    <input type="text" class="mcq-choice-input" value="${question.option_b || ''}" placeholder="Enter Choice B" style="flex: 1; border-left: 3px solid #11224a; background: #25477a; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none;">
                                </div>
                                <div class="choice-item" data-choice="C" style="display: flex; align-items: center; gap: 1rem;">
                                    <span class="mcq-choice-box-input" style="background: #25477a; color: #ffe600; width: 2.2rem; height: 2.2rem; border-radius: 0.4rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">C</span>
                                    <input type="text" class="mcq-choice-input" value="${question.option_c || ''}" placeholder="Enter Choice C" style="flex: 1; border-left: 3px solid #11224a; background: #25477a; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none;">
                                </div>
                                <div class="choice-item" data-choice="D" style="display: flex; align-items: center; gap: 1rem;">
                                    <span class="mcq-choice-box-input" style="background: #25477a; color: #ffe600; width: 2.2rem; height: 2.2rem; border-radius: 0.4rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">D</span>
                                    <input type="text" class="mcq-choice-input" value="${question.option_d || ''}" placeholder="Enter Choice D" style="flex: 1; border-left: 3px solid #11224a; background: #25477a; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none;">
                                </div>
                                <div class="choice-item" data-choice="E" style="display: flex; align-items: center; gap: 1rem;">
                                    <span class="mcq-choice-box-input" style="background: #25477a; color: #ffe600; width: 2.2rem; height: 2.2rem; border-radius: 0.4rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">E</span>
                                    <input type="text" class="mcq-choice-input" value="${question.option_e || ''}" placeholder="Enter Choice E" style="flex: 1; border-left: 3px solid #11224a; background: #25477a; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none;">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Correct Answer Section -->
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600; font-size: 1.1rem;">Correct Answer:</label>
                            <select class="mcq-correct-answer" style="background: #25477a; border: none; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none; width: 100%;">
                                <option value="">Select correct answer</option>
                                ${question.option_a && question.option_a.trim() !== '' ? `<option value="A" ${question.correct_answer == 'A' ? 'selected' : ''}>A</option>` : ''}
                                ${question.option_b && question.option_b.trim() !== '' ? `<option value="B" ${question.correct_answer == 'B' ? 'selected' : ''}>B</option>` : ''}
                                ${question.option_c && question.option_c.trim() !== '' ? `<option value="C" ${question.correct_answer == 'C' ? 'selected' : ''}>C</option>` : ''}
                                ${question.option_d && question.option_d.trim() !== '' ? `<option value="D" ${question.correct_answer == 'D' ? 'selected' : ''}>D</option>` : ''}
                                ${question.option_e && question.option_e.trim() !== '' ? `<option value="E" ${question.correct_answer == 'E' ? 'selected' : ''}>E</option>` : ''}
                            </select>
                        </div>
                        
                        <button type="button" class="mcq-delete-btn" onclick="deleteQuestion('${question.question_id}', 'mcq', this)" style="background: #e74c3c; color: #fff; border: none; border-radius: 0.5rem; padding: 0.75rem 1.5rem; font-size: 1rem; cursor: pointer; margin-top: 1rem;">Delete Question</button>
                    </div>
                `;
            });
    }
    
    html += `
        </div>
        <div style="display:flex; justify-content: space-between; align-items:center; margin-top: 2rem;">
            <button type="button" onclick="addNewMCQQuestion()" class="save-btn" style="background:#ffe600; color:#002147;">+ Add Question</button>
            <button type="button" class="save-btn" onclick="saveQuestions('mcq')">Update</button>
        </div>
    `;
    
    return html;
}

// Helper function to count available choices
function getChoicesCount(question) {
    let count = 0;
    if (question.option_a && question.option_a.trim() !== '') count++;
    if (question.option_b && question.option_b.trim() !== '') count++;
    if (question.option_c && question.option_c.trim() !== '') count++;
    if (question.option_d && question.option_d.trim() !== '') count++;
    if (question.option_e && question.option_e.trim() !== '') count++;
    return count;
}

// Function to add event listeners for MCQ choices count
function addMCQChoicesEventListeners() {
    const questionsContainer = document.getElementById('mcq-questions-container');
    if (!questionsContainer) return;
    
    // Trigger change event on all choices count selects to set initial state
    questionsContainer.querySelectorAll('.mcq-choices-count').forEach(select => {
        select.dispatchEvent(new Event('change'));
    });
}

// Function to update choices visibility
function updateChoicesVisibility(choicesList, count) {
    const choices = choicesList.querySelectorAll('.choice-item');
    
    console.log('Updating visibility for', choices.length, 'choices to show', count);
    
    choices.forEach((choice, index) => {
        if (index < count) {
            choice.classList.remove('hidden');
            console.log('Showing choice', index + 1, '(', choice.dataset.choice, ')');
        } else {
            choice.classList.add('hidden');
            console.log('Hiding choice', index + 1, '(', choice.dataset.choice, ')');
        }
    });
}

// Function to update correct answer options
function updateCorrectAnswerOptions(correctAnswerSelect, count) {
    const letters = ['A', 'B', 'C', 'D', 'E'];
    const currentValue = correctAnswerSelect.value;
    
    // Clear existing options except the first one
    correctAnswerSelect.innerHTML = '<option value="">Select correct answer</option>';
    
    // Add options based on count
    for (let i = 0; i < count; i++) {
        const option = document.createElement('option');
        option.value = letters[i];
        option.textContent = letters[i];
        if (letters[i] === currentValue) {
            option.selected = true;
        }
        correctAnswerSelect.appendChild(option);
    }
    
    // If current value is not in the new range, reset to empty
    if (currentValue && !letters.slice(0, count).includes(currentValue)) {
        correctAnswerSelect.value = '';
        console.log('Reset correct answer because', currentValue, 'is not valid for', count, 'choices');
    }
}

// Function to handle choices count change (called from HTML onchange)
function handleChoicesCountChange(selectElement) {
    const questionItem = selectElement.closest('.mcq-question-item');
    const choicesList = questionItem.querySelector('.mcq-choices-list');
    const correctAnswerSelect = questionItem.querySelector('.mcq-correct-answer');
    const selectedCount = parseInt(selectElement.value);
    
    console.log('Choices count changed to:', selectedCount);
    
    // Show/hide choices based on selected count
    updateChoicesVisibility(choicesList, selectedCount);
    
    // Clear hidden choice values
    clearHiddenChoiceValues(choicesList, selectedCount);
    
    // Update correct answer options
    updateCorrectAnswerOptions(correctAnswerSelect, selectedCount);
}

// Function to clear hidden choice values
function clearHiddenChoiceValues(choicesList, count) {
    const choices = choicesList.querySelectorAll('.choice-item');
    
    choices.forEach((choice, index) => {
        if (index >= count) {
            // Clear the input value for hidden choices
            const input = choice.querySelector('.mcq-choice-input');
            if (input) {
                input.value = '';
                console.log('Cleared choice', index + 1, '(', choice.dataset.choice, ')');
                
                // Add visual feedback
                input.style.backgroundColor = '#e74c3c';
                input.style.color = '#fff';
                setTimeout(() => {
                    input.style.backgroundColor = '#25477a';
                    input.style.color = '#fff';
                }, 500);
            }
        }
    });
}

function generateIDHTML(questions, settings) {
    let html = `
        <h2 style="margin-bottom: 2rem;">Edit Identification Questions</h2>
        
        <!-- Settings Section -->
        <div style="background: #17305c; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 2rem; ">
            <div style="display: flex; justify-content: space-between; align-items: center; ">
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <div style="display:flex; align-items:center; gap:0.6rem;">
                        <label style="color: #b3c6e0; font-weight: 600;">Shuffle Questions:</label>
                        <select id="id-shuffle" style="background: #25477a; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; color: #fff; font-size: 1rem; outline: none;">
                            <option value="1" ${settings && settings.shuffle_identification === 1 ? 'selected' : ''}>On</option>
                            <option value="0" ${settings && settings.shuffle_identification === 0 ? 'selected' : ''}>Off</option>
                        </select>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.6rem;">
                        <label style="color: #b3c6e0; font-weight: 600;">Auto check student answer using AI:</label>
                        <select id="id-ai-check" style="background: #25477a; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; color: #fff; font-size: 1rem; outline: none;">
                            <option value="1" ${settings && settings.ai_check_identification === 1 ? 'selected' : ''}>On</option>
                            <option value="0" ${settings && settings.ai_check_identification === 0 ? 'selected' : ''}>Off</option>
                        </select>
                    </div>
                </div>
                <div style="color: #b3c6e0; font-weight: 600;">
                    Questions made: <span style="color: #ffe600;">${questions.length}</span>
                </div>
            </div>
        </div>
        
        <div id="id-questions-container">
    `;
    
    if (questions.length === 0) {
        html += '<p style="text-align: center; color: #b3c6e0;">No questions found</p>';
    } else {
        questions.forEach((question, index) => {
            html += `
                <div class="id-question-item" data-question-id="${question.question_id}">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Question ${index + 1} ${!question.question_id ? '<span style=\"background:#777; color:#fff; font-size:0.8rem; padding:0.1rem 0.4rem; border-radius:0.3rem; margin-left:0.4rem;\">Unsaved</span>' : ''}</label>
                        <input type="text" class="id-question-input" value="${question.question_text}" placeholder="Enter Question" style="width: 100%; background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Points</label>
                        <select class="id-points" style="background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none;">
                            <option value="1" ${question.points == 1 ? 'selected' : ''}>1</option>
                            <option value="2" ${question.points == 2 ? 'selected' : ''}>2</option>
                            <option value="3" ${question.points == 3 ? 'selected' : ''}>3</option>
                            <option value="4" ${question.points == 4 ? 'selected' : ''}>4</option>
                            <option value="5" ${question.points == 5 ? 'selected' : ''}>5</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Correct Answer</label>
                        <input type="text" class="id-correct-answer" value="${question.correct_answer}" placeholder="Enter Correct Answer" style="width: 100%; background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none; box-sizing: border-box;">
                    </div>
                    <button type="button" class="mcq-delete-btn" onclick="deleteQuestion('${question.question_id}', 'id', this)" style="background: #e74c3c; color: #fff; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.9rem; cursor: pointer; margin-top: 1rem;">Delete Question</button>
                    <hr style="margin: 2rem 0; border-color: #25477a;">
                </div>
            `;
        });
    }
    
    html += `
        </div>
        <div style="display:flex; justify-content: space-between; align-items:center; margin-top: 2rem;">
            <button type="button" onclick="addNewIDQuestion()" class="save-btn" style="background:#ffe600; color:#002147;">+ Add Question</button>
            <button type="button" class="save-btn" onclick="saveQuestions('id')">Update</button>
        </div>
    `;
    
    return html;
}

function generateTFHTML(questions, settings) {
    let html = `
        <h2 style="margin-bottom: 2rem;">Edit True or False Questions</h2>
        
        <!-- Settings Section -->
        <div style="background: #17305c; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; ">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <label style="color: #b3c6e0; font-weight: 600;">Shuffle Questions:</label>
                    <select id="tf-shuffle" style="background: #25477a; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; color: #fff; font-size: 1rem; outline: none;">
                        <option value="1" ${settings && settings.shuffle_true_false === 1 ? 'selected' : ''}>On</option>
                        <option value="0" ${settings && settings.shuffle_true_false === 0 ? 'selected' : ''}>Off</option>
                    </select>
                </div>
                <div style="color: #b3c6e0; font-weight: 600;">
                    Questions made: <span style="color: #ffe600;">${questions.length}</span>
                </div>
            </div>
        </div>
        
        <div id="tf-questions-container">
    `;
    
    if (questions.length === 0) {
        html += '<p style="text-align: center; color: #b3c6e0;">No questions found</p>';
    } else {
        questions.forEach((question, index) => {
            html += `
                <div class="tf-question-item" data-question-id="${question.question_id}">
                                        <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Question ${index + 1} ${!question.question_id ? '<span style=\"background:#777; color:#fff; font-size:0.8rem; padding:0.1rem 0.4rem; border-radius:0.3rem; margin-left:0.4rem;\">Unsaved</span>' : ''}</label>
                        <input type="text" class="tf-question-input" value="${question.question_text}" placeholder="Enter Question" style="width: 100%; background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Points</label>
                        <select class="tf-points" style="background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none;">
                            <option value="1" ${question.points == 1 ? 'selected' : ''}>1</option>
                            <option value="2" ${question.points == 2 ? 'selected' : ''}>2</option>
                            <option value="3" ${question.points == 3 ? 'selected' : ''}>3</option>
                            <option value="4" ${question.points == 4 ? 'selected' : ''}>4</option>
                            <option value="5" ${question.points == 5 ? 'selected' : ''}>5</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Correct Answer</label>
                        <select class="tf-correct-answer" style="background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none;">
                        <option value="1" ${question.correct_answer == 1 ? 'selected' : ''}>True</option>
                            <option value="0" ${question.correct_answer == 0 ? 'selected' : ''}>False</option>
                        </select>
                    </div>
                    <button type="button" class="mcq-delete-btn" onclick="deleteQuestion('${question.question_id}', 'tf', this)" style="background: #e74c3c; color: #fff; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.9rem; cursor: pointer; margin-top: 1rem;">Delete Question</button>
                    <hr style="margin: 2rem 0; border-color: #25477a;">
                </div>
            `;
        });
    }
    
    html += `
        </div>
        <div style="display:flex; justify-content: space-between; align-items:center; margin-top: 2rem;">
            <button type="button" onclick="addNewTFQuestion()" class="save-btn" style="background:#ffe600; color:#002147;">+ Add Question</button>
            <button type="button" class="save-btn" onclick="saveQuestions('tf')">Update</button>
        </div>
    `;
    
    return html;
}

function deleteQuestion(questionId, type, el) {
    // Queue deletion instead of immediate API call
    const assessmentId = document.getElementById('assessment_id').value;
    const container = document.getElementById(`${type}-questions-container`);
    if (!pendingQuestionChanges[type]) {
        pendingQuestionChanges[type] = {
            assessment_id: assessmentId,
            question_type: type,
            questions: [],
            shuffle_settings: {}
        };
    }
    // Ensure structure
    if (!pendingQuestionChanges[type].deleted_question_ids) {
        pendingQuestionChanges[type].deleted_question_ids = [];
    }

    const item = el ? el.closest(`.${type}-question-item, .mcq-question-item, .id-question-item, .tf-question-item`) : null;
    if (!questionId) {
        // Unsaved draft: remove DOM and also remove from queued drafts if present
        if (item) item.remove();
        const queued = pendingQuestionChanges[type].questions || [];
        pendingQuestionChanges[type].questions = queued.filter(q => q._domRemoved || q.question_id); // drop drafts without id
        return;
    }

    // For existing question: mark for deletion and visually remove
    pendingQuestionChanges[type].deleted_question_ids.push(questionId);
    if (item) item.remove();
}

function saveQuestions(type) {
    const questions = [];
    const container = document.getElementById(`${type}-questions-container`);
    let hasErrors = false;
    
    // Collect shuffle settings - look in the entire modal, not just the questions container
    let shuffleSettings = {};
    if (type === 'mcq') {
        const shuffleSelect = document.querySelector('#mcq-shuffle');
        if (shuffleSelect) {
            shuffleSettings.shuffle_mcq = shuffleSelect.value;
            console.log('MCQ shuffle setting collected:', shuffleSelect.value);
        }
    } else if (type === 'id') {
        const shuffleSelect = document.querySelector('#id-shuffle');
        if (shuffleSelect) {
            shuffleSettings.shuffle_identification = shuffleSelect.value;
            console.log('ID shuffle setting collected:', shuffleSelect.value);
        }
    } else if (type === 'tf') {
        const shuffleSelect = document.querySelector('#tf-shuffle');
        if (shuffleSelect) {
            shuffleSettings.shuffle_true_false = shuffleSelect.value;
            console.log('TF shuffle setting collected:', shuffleSelect.value);
        }
    }
    
    console.log('Collected shuffle settings:', shuffleSettings);
    
    if (type === 'mcq') {
        container.querySelectorAll('.mcq-question-item').forEach((item, index) => {
            const questionId = item.dataset.questionId || null;
            const questionText = item.querySelector('.mcq-question-input').value;
            const points = item.querySelector('.mcq-points').value;
            
            // Get the selected choices count
            const choicesCount = parseInt(item.querySelector('.mcq-choices-count').value);
            
            // Get all choice inputs from the choices list
            const choicesList = item.querySelector('.mcq-choices-list');
            const choiceInputs = choicesList.querySelectorAll('.mcq-choice-input');
            
            // Get values for each choice (A, B, C, D, E)
            let optionA = choiceInputs[0] ? choiceInputs[0].value : '';
            let optionB = choiceInputs[1] ? choiceInputs[1].value : '';
            let optionC = choiceInputs[2] ? choiceInputs[2].value : '';
            let optionD = choiceInputs[3] ? choiceInputs[3].value : '';
            let optionE = choiceInputs[4] ? choiceInputs[4].value : '';
            
            // Clear options that are beyond the selected count
            if (choicesCount < 4) optionD = '';
            if (choicesCount < 5) optionE = '';

            // Reset previous error styles
            item.querySelector('.mcq-question-input').classList.remove('error-bottom');
            choiceInputs.forEach(input => input.classList.remove('error-left'));

            // Validate question text
            if (!questionText || questionText.trim() === '') {
                item.querySelector('.mcq-question-input').classList.add('error-bottom');
                hasErrors = true;
            }

            // Validate visible choices up to selected count
            choiceInputs.forEach((input, idx) => {
                if (idx < choicesCount) {
                    if (!input.value || input.value.trim() === '') {
                        input.classList.add('error-left');
                        hasErrors = true;
                    }
                }
            });
            
            let correctAnswer = item.querySelector('.mcq-correct-answer').value;
            
            // Validate correct answer based on choices count
            const letters = ['A', 'B', 'C', 'D', 'E'];
            if (correctAnswer && !letters.slice(0, choicesCount).includes(correctAnswer)) {
                correctAnswer = ''; // Reset invalid correct answer
                console.log('Invalid correct answer reset for question', index + 1);
            }
            
            questions.push({
                question_id: questionId,
                question_text: questionText,
                points: points,
                option_a: optionA,
                option_b: optionB,
                option_c: optionC,
                option_d: optionD,
                option_e: optionE,
                correct_answer: correctAnswer
            });
        });
    } else if (type === 'id') {
        container.querySelectorAll('.id-question-item').forEach((item, index) => {
            const questionId = item.dataset.questionId || null;
            const questionText = item.querySelector('.id-question-input').value;
            const points = item.querySelector('.id-points').value;
            const correctAnswer = item.querySelector('.id-correct-answer').value;

            // Reset previous error styles
            item.querySelector('.id-question-input').classList.remove('error-bottom');
            item.querySelector('.id-correct-answer').classList.remove('error-bottom');

            // Validate required fields
            if (!questionText || questionText.trim() === '') {
                item.querySelector('.id-question-input').classList.add('error-bottom');
                hasErrors = true;
            }
            if (!correctAnswer || correctAnswer.trim() === '') {
                item.querySelector('.id-correct-answer').classList.add('error-bottom');
                hasErrors = true;
            }
            
            questions.push({
                question_id: questionId,
                question_text: questionText,
                points: points,
                correct_answer: correctAnswer
            });
        });
    } else if (type === 'tf') {
        container.querySelectorAll('.tf-question-item').forEach((item, index) => {
            const questionId = item.dataset.questionId || null;
            const questionText = item.querySelector('.tf-question-input').value;
            const points = item.querySelector('.tf-points').value;
            const correctAnswer = item.querySelector('.tf-correct-answer').value;

            // Reset previous error styles
            item.querySelector('.tf-question-input').classList.remove('error-bottom');

            // Validate required fields
            if (!questionText || questionText.trim() === '') {
                item.querySelector('.tf-question-input').classList.add('error-bottom');
                hasErrors = true;
            }
            
            questions.push({
                question_id: questionId,
                question_text: questionText,
                points: points,
                correct_answer: correctAnswer
            });
        });
    }
    
    // If validation failed, block saving
    if (hasErrors) {
        alert('Please fill in the highlighted fields before saving.');
        return;
    }

    // Queue changes instead of saving immediately (preserve any queued deletions)
    const existingDeleted = (pendingQuestionChanges[type] && pendingQuestionChanges[type].deleted_question_ids)
        ? pendingQuestionChanges[type].deleted_question_ids
        : [];
    if (type === 'id') {
        const aiSelect = document.getElementById('id-ai-check');
        if (aiSelect) {
            shuffleSettings.ai_check_identification = parseInt(aiSelect.value);
        }
    }
    pendingQuestionChanges[type] = {
            assessment_id: document.getElementById('assessment_id').value,
            question_type: type,
            questions: questions,
        shuffle_settings: shuffleSettings,
        deleted_question_ids: existingDeleted
    };
    alert('Updates queued. Click "Save Changes" on the main page to apply.');
            document.getElementById('questionsModal').style.display = 'none';
}

function saveAssessment() {
    // Show loading state
    const saveBtn = document.getElementById('mainSaveBtn');
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    const formData = {
        assessment_id: document.getElementById('assessment_id').value,
        title: document.getElementById('title').value,
        year_course: document.getElementById('year_course').value,
        sections: document.getElementById('section').value,
        course_code: document.getElementById('course_code').value,
        timer: document.getElementById('timer').value,
        status: document.getElementById('status').value,
        school_year: document.getElementById('school_year').value,
        schedule_date: document.getElementById('schedule_date').value,
        schedule_time: document.getElementById('schedule_time').value,
        close_date: document.getElementById('close_date').value,
        close_time: document.getElementById('close_time').value
    };
    
    const postJson = (url, payload) => fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) }).then(r => r.json());

    const updates = [];
    ['mcq','id','tf'].forEach(t => { if (pendingQuestionChanges[t]) updates.push(pendingQuestionChanges[t]); });

    const applyQuestionUpdates = updates.reduce((p, u) => p.then(() => postJson('phpfiles/update_questions.php', u)), Promise.resolve());

    applyQuestionUpdates
    .then(() => postJson('phpfiles/update_assessment.php', formData))
    .then(data => {
        if (data.success) {
            alert('Assessment updated successfully');
            pendingQuestionChanges = { mcq: null, id: null, tf: null };
            location.reload();
        } else {
            alert('Error updating assessment: ' + data.message);
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating assessment');
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

function deleteAssessment() {
    if (confirm('Are you sure you want to delete this assessment? This action cannot be undone.')) {
        const assessmentId = document.getElementById('assessment_id').value;
        
        fetch('phpfiles/delete_assessment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                assessment_id: assessmentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assessment deleted successfully');
                window.location.href = 'assessments.php';
            } else {
                alert('Error deleting assessment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting assessment');
        });
    }
}

// Close modal when clicking the close button
document.getElementById('closeQuestionsModal').addEventListener('click', function() {
    document.getElementById('questionsModal').style.display = 'none';
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('questionsModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

// Floating button functionality
document.querySelector('.floating-btn').addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});


// Add new MCQ question template
function addNewMCQQuestion() {
    const container = document.getElementById('mcq-questions-container');
    if (!container) return;
    const index = container.querySelectorAll('.mcq-question-item').length;
    const template = `
        <div class="mcq-question-item" data-question-id="" style="background: #17305c; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: #fff; font-size: 1.5rem; font-weight: 700; margin: 0;">Question #${index + 1}</h3>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <label style="color: #b3c6e0; font-weight: 600;">Point/s:</label>
                    <select class="mcq-points" style="background: #25477a; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; color: #fff; font-size: 1rem; outline: none;">
                        <option value="1" selected>1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <input type="text" class="mcq-question-input" value="" placeholder="Enter Question" style="width: 100%; background: #25477a; border: none; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1.1rem; outline: none;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <label style="color: #b3c6e0; font-weight: 600; font-size: 1.1rem;">Choices:</label>
                    <select class="mcq-choices-count" onchange="handleChoicesCountChange(this)" style="background: #25477a; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; color: #fff; font-size: 1rem; outline: none;">
                        <option value="3" selected>3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="mcq-choices-list" style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="choice-item" data-choice="A" style="display: flex; align-items: center; gap: 1rem;">
                        <span class="mcq-choice-box-input" style="background: #25477a; color: #ffe600; width: 2.2rem; height: 2.2rem; border-radius: 0.4rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">A</span>
                        <input type="text" class="mcq-choice-input" value="" placeholder="Enter Choice A" style="flex: 1; border-left: 3px solid #11224a; background: #25477a; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none;">
                    </div>
                    <div class="choice-item" data-choice="B" style="display: flex; align-items: center; gap: 1rem;">
                        <span class="mcq-choice-box-input" style="background: #25477a; color: #ffe600; width: 2.2rem; height: 2.2rem; border-radius: 0.4rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">B</span>
                        <input type="text" class="mcq-choice-input" value="" placeholder="Enter Choice B" style="flex: 1; border-left: 3px solid #11224a; background: #25477a; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none;">
                    </div>
                    <div class="choice-item" data-choice="C" style="display: flex; align-items: center; gap: 1rem;">
                        <span class="mcq-choice-box-input" style="background: #25477a; color: #ffe600; width: 2.2rem; height: 2.2rem; border-radius: 0.4rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">C</span>
                        <input type="text" class="mcq-choice-input" value="" placeholder="Enter Choice C" style="flex: 1; border-left: 3px solid #11224a; background: #25477a; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none;">
                    </div>
                    <div class="choice-item" data-choice="D" style="display: flex; align-items: center; gap: 1rem;">
                        <span class="mcq-choice-box-input" style="background: #25477a; color: #ffe600; width: 2.2rem; height: 2.2rem; border-radius: 0.4rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">D</span>
                        <input type="text" class="mcq-choice-input" value="" placeholder="Enter Choice D" style="flex: 1; border-left: 3px solid #11224a; background: #25477a; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none;">
                    </div>
                    <div class="choice-item" data-choice="E" style="display: flex; align-items: center; gap: 1rem;">
                        <span class="mcq-choice-box-input" style="background: #25477a; color: #ffe600; width: 2.2rem; height: 2.2rem; border-radius: 0.4rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">E</span>
                        <input type="text" class="mcq-choice-input" value="" placeholder="Enter Choice E" style="flex: 1; border-left: 3px solid #11224a; background: #25477a; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none;">
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600; font-size: 1.1rem;">Correct Answer:</label>
                <select class="mcq-correct-answer" style="background: #25477a; border: none; border-radius: 0.5rem; padding: 1rem; color: #fff; font-size: 1rem; outline: none; width: 100%;">
                    <option value="">Select correct answer</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </div>
            <button type="button" class="mcq-delete-btn" onclick="deleteQuestion('', 'mcq', this)" style="background: #e74c3c; color: #fff; border: none; border-radius: 0.5rem; padding: 0.75rem 1.5rem; font-size: 1rem; cursor: pointer; margin-top: 1rem;">Delete Question</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', template);
    // Initialize visibility and correct answer options for the new item
    const newItem = container.lastElementChild;
    const selectCount = newItem.querySelector('.mcq-choices-count');
    if (selectCount) selectCount.dispatchEvent(new Event('change'));
}

// Add new Identification question template
function addNewIDQuestion() {
    const container = document.getElementById('id-questions-container');
    if (!container) return;
    const index = container.querySelectorAll('.id-question-item').length;
    const template = `
        <div class="id-question-item" data-question-id="">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Question ${index + 1}</label>
                <input type="text" class="id-question-input" value="" placeholder="Enter Question" style="width: 100%; background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Points</label>
                <select class="id-points" style="background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none;">
                    <option value="1" selected>1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Correct Answer</label>
                <input type="text" class="id-correct-answer" value="" placeholder="Enter Correct Answer" style="width: 100%; background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none; box-sizing: border-box;">
            </div>
            <button type="button" class="mcq-delete-btn" onclick="deleteQuestion('', 'id', this)" style="background: #e74c3c; color: #fff; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.9rem; cursor: pointer; margin-top: 1rem;">Delete Question</button>
            <hr style="margin: 2rem 0; border-color: #25477a;">
        </div>
    `;
    container.insertAdjacentHTML('beforeend', template);
}

// Add new True/False question template
function addNewTFQuestion() {
    const container = document.getElementById('tf-questions-container');
    if (!container) return;
    const index = container.querySelectorAll('.tf-question-item').length;
    const template = `
        <div class="tf-question-item" data-question-id="">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Question ${index + 1}</label>
                <input type="text" class="tf-question-input" value="" placeholder="Enter Question" style="box-sizing: border-box; width: 100%; background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Points</label>
                <select class="tf-points" style="background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none;">
                    <option value="1" selected>1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #b3c6e0; font-weight: 600;">Correct Answer</label>
                <select class="tf-correct-answer" style="background: #17305c; border: none; border-radius: 0.5rem; padding: 0.625rem 0.875rem; color: #fff; font-size: 1rem; outline: none;">
                    <option value="1">True</option>
                    <option value="0">False</option>
                </select>
            </div>
            <button type="button" class="mcq-delete-btn" onclick="deleteQuestion('', 'tf', this)" style="background: #e74c3c; color: #fff; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.9rem; cursor: pointer; margin-top: 1rem;">Delete Question</button>
            <hr style="margin: 2rem 0; border-color: #25477a;">
        </div>
    `;
    container.insertAdjacentHTML('beforeend', template);
}
</script>
</body>
</html>
