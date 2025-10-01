<?php require_once 'auth/check_session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Report | Exam Secure</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/reports-style.css">
    <link rel="stylesheet" href="css/report-assessment-style.css">
    <link rel="stylesheet" href="css/monitoring-assessment-style.css">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="reports-container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <?php
        require_once 'database/db_config.php';

        if (!isset($_GET['id'])) {
            header('Location: reports.php');
            exit();
        }

        $assessment_id = $_GET['id'];
        $owner_id = $_SESSION['unique_id'];

        // Verify ownership and fetch assessment meta
        $meta_sql = "SELECT title, year_course, sections, course_code, timer, status, schedule, closing_time, school_year, access_code,
                             shuffle_mcq, shuffle_identification, shuffle_true_false, ai_check_identification, created_at, updated_at
                     FROM created_assessments WHERE unique_id = ? AND owner_id = ?";
        $meta_stmt = $conn->prepare($meta_sql);
        $meta_stmt->bind_param("ss", $assessment_id, $owner_id);
        $meta_stmt->execute();
        $meta_result = $meta_stmt->get_result();
        if ($meta_result->num_rows === 0) {
            header('Location: reports.php');
            exit();
        }
        $assessment = $meta_result->fetch_assoc();

        // Build Sections list for filter
        $sections = [];
        $sec_sql = "SELECT DISTINCT year_section FROM assessment_sessions WHERE assessment_id = ? AND year_section IS NOT NULL AND year_section != '' ORDER BY year_section";
        $sec_stmt = $conn->prepare($sec_sql);
        $sec_stmt->bind_param("s", $assessment_id);
        $sec_stmt->execute();
        $sec_res = $sec_stmt->get_result();
        while ($r = $sec_res->fetch_assoc()) { $sections[] = $r['year_section']; }

        // Compute total points for this assessment
        $total_points = 0;
        $points_sql = "SELECT 
            COALESCE((SELECT SUM(points) FROM multiple_choice_questions WHERE assessment_id = ?),0)
          + COALESCE((SELECT SUM(points) FROM identification_questions WHERE assessment_id = ?),0)
          + COALESCE((SELECT SUM(points) FROM true_false_questions WHERE assessment_id = ?),0) AS total_points";
        $points_stmt = $conn->prepare($points_sql);
        $points_stmt->bind_param("sss", $assessment_id, $assessment_id, $assessment_id);
        $points_stmt->execute();
        $points_res = $points_stmt->get_result();
        if ($rowp = $points_res->fetch_assoc()) { $total_points = (int)($rowp['total_points'] ?? 0); }

        // Fetch student submissions summary
        $students = [];
        $sub_sql = "SELECT 
                        s.session_id,
                        s.student_id,
                        s.student_name,
                        s.year_section,
                        s.started_at,
                        s.status,
                        s.completed_at,
                        s.cheating_flag,
                        s.tab_switch_count,
                        s.face_left_count,
                        s.face_right_count,
                        s.suspicious_count,
                        s.screenshot_count,
                        (s.tab_switch_count + s.face_left_count + s.face_right_count) as total_warnings,
                        COALESCE(SUM(COALESCE(r.points_earned,0)), 0) as total_score,
                        MIN(r.submitted_at) as first_submit_time,
                        MAX(r.submitted_at) as last_submit_time
                    FROM assessment_sessions s
                    LEFT JOIN student_responses r 
                        ON r.assessment_id = s.assessment_id
                        AND (
                            r.session_id = s.session_id
                            OR (
                                r.session_id IS NULL 
                                AND r.student_id = s.student_id 
                                AND r.submitted_at >= s.started_at 
                                AND (s.completed_at IS NULL OR r.submitted_at <= s.completed_at)
                            )
                        )
                    WHERE s.assessment_id = ?
                    GROUP BY s.session_id, s.student_id, s.student_name, s.year_section, s.status, s.completed_at, s.cheating_flag, total_warnings
                    ORDER BY s.completed_at IS NULL, s.completed_at DESC";
        $sub_stmt = $conn->prepare($sub_sql);
        $sub_stmt->bind_param("s", $assessment_id);
        $sub_stmt->execute();
        $sub_result = $sub_stmt->get_result();
        while ($row = $sub_result->fetch_assoc()) {
            $students[] = $row;
        }
        ?>

        <div class="header">
            <button class="back-button" onclick="window.location.href='reports.php'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Reports
            </button>
        </div>
        
        <div id="reportContent">
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
                        <div id="evidenceCarousel" class="evidence-carousel"></div>
                    </div>
                    <button class="carousel-btn next" onclick="nextEvidence()">&#8250;</button>
                </div>
                <div class="carousel-indicators" id="carouselIndicators"></div>
            </div>
            <div class="student-details-section">
                <h3>Student Details</h3>
                <div id="studentDetails"></div>
            </div>
        </div>
    </div>
</div>

<div id="answersModal" class="modal">
    <div class="modal-content" style="max-width:1100px;">
        <div class="modal-header">
            <h2>Student Answers</h2>
            <button class="close-btn" onclick="closeAnswersModal()">&times;</button>
        </div>
        <div class="modal-body" style="flex-direction:column;">
            <div class="answers-summary">
                <div class="summary-grid">
                    <div><span class="sum-label">Name</span><span class="sum-value" id="ansName">-</span></div>
                    <div><span class="sum-label">Section</span><span class="sum-value" id="ansSection">-</span></div>
                    <div><span class="sum-label">Started</span><span class="sum-value" id="ansStarted">-</span></div>
                    <div><span class="sum-label">Completed</span><span class="sum-value" id="ansCompleted">-</span></div>
                    <div><span class="sum-label">Score</span><span class="sum-value" id="ansScore">-</span></div>
                    <div><span class="sum-label">Questions</span><span class="sum-value" id="ansCount">-</span></div>
                    <div><span class="sum-label">Time Spent</span><span class="sum-value" id="ansTime">-</span></div>
                </div>
            </div>
            <div class="section-divider" style="margin:12px 0;"></div>
            <div class="answers-controls">
                <div class="chip-group">
                    <span class="chip" id="ansTotalChip">0 Qs</span>
                    <span class="chip success" id="ansCorrectChip">0 Correct</span>
                    <span class="chip danger" id="ansIncorrectChip">0 Incorrect</span>
                </div>
                <div class="controls-right">
                    <label class="toggle"><input type="checkbox" id="onlyIncorrect"> Show incorrect only</label>
                    <input type="text" id="ansSearch" class="search-input small" placeholder="Search question or answer">
                </div>
            </div>
            <div class="answers-list" id="answersList"></div>
        </div>
    </div>
    
</div>

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

       
      

       
        <div class="assessment-info-section">
            <div class="assessment-info">
                <h1 class="assessment-title"><?= htmlspecialchars($assessment['title']) ?></h1>
                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="meta-label">Year & Course</div>
                        <div class="meta-value"><?= htmlspecialchars($assessment['year_course']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Sections</div>
                        <div class="meta-value"><?= htmlspecialchars($assessment['sections']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Course Code</div>
                        <div class="meta-value"><?= htmlspecialchars($assessment['course_code']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Timer</div>
                        <div class="meta-value"><?= isset($assessment['timer']) && (int)$assessment['timer'] > 0 ? (int)$assessment['timer'].' minutes' : '—' ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Status</div>
                        <div class="meta-value status"><?= strtoupper($assessment['status']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">School Year</div>
                        <div class="meta-value"><?= htmlspecialchars($assessment['school_year']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Start</div>
                        <div class="meta-value"><?= !empty($assessment['schedule']) ? date('m/d/Y · h:i A', strtotime($assessment['schedule'])) : '—' ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Close Time</div>
                        <div class="meta-value"><?= !empty($assessment['closing_time']) ? date('m/d/Y · h:i A', strtotime($assessment['closing_time'])) : '—' ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Access Code</div>
                        <div class="meta-value code"><?= htmlspecialchars($assessment['access_code']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Shuffle MCQ</div>
                        <div class="meta-value"><?= !empty($assessment['shuffle_mcq']) ? 'ON' : 'OFF' ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Shuffle Identification</div>
                        <div class="meta-value"><?= !empty($assessment['shuffle_identification']) ? 'ON' : 'OFF' ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Shuffle True/False</div>
                        <div class="meta-value"><?= !empty($assessment['shuffle_true_false']) ? 'ON' : 'OFF' ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">AI Check Identification</div>
                        <div class="meta-value"><?= !empty($assessment['ai_check_identification']) ? 'ON' : 'OFF' ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Created</div>
                        <div class="meta-value"><?= !empty($assessment['created_at']) ? date('m/d/Y', strtotime($assessment['created_at'])) : '—' ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Updated</div>
                        <div class="meta-value"><?= !empty($assessment['updated_at']) ? date('m/d/Y', strtotime($assessment['updated_at'])) : '—' ?></div>
                    </div>
                </div>
            </div>
        </div>

      
        <div class="section-divider"></div>
        <div class="filters-row" style="margin: 8px 0 6px 0;">
            <br>
            <div class="total-submissions" style="color:#fff; font-weight:600;">Total Submissions : <span class="highlight-number"><?= array_sum(array_map(fn($s)=> $s['status']==='completed'?1:0, $students)) ?> Students</span> </div>
            <br>
            <br>
            <br>
            <br>
       
        </div>
        <div class="filters-row" style="margin: 0 0 14px 0;">
            <label for="sectionFilter" style="color:#b3c6e0; font-weight:600;">Section :</label>
            <select id="sectionFilter" class="filter-dropdown">
                <option value="">All</option>
                <?php foreach ($sections as $sec): ?>
                    <option value="<?= htmlspecialchars($sec) ?>"><?= htmlspecialchars($sec) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="statusFilter" style="color:#b3c6e0; font-weight:600;">Status :</label>
            <select id="statusFilter" class="filter-dropdown">
                <option value="">All</option>
                <option value="answering">Answering</option>
                <option value="finished">Finished</option>
                <option value="flagged">Cheating</option>
            </select>
            <div class="search-group" style="min-width:320px; margin-left:auto;">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b3c6e0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>
                <input type="text" id="studentSearch" class="search-input" placeholder="Search student" />
            </div>
        </div>

        <div class="reports-list" style="background:#0f2a4a; border-radius:12px; padding:0; overflow:hidden;">
            <table id="studentsTable" style="width:100%; border-collapse:collapse;">
                <thead style="background:#17305c; color:#fff;">
                    <tr>
                        <th style="padding:12px 14px; text-align:left;">Name</th>
                        <th style="padding:12px 14px; text-align:left;">Cheated</th>
                        <th style="padding:12px 14px; text-align:left;">Status</th>
                        <th style="padding:12px 14px; text-align:left;">Submitted</th>
                        <th style="padding:12px 14px; text-align:left;">Started • Ended</th>
                        <th style="padding:12px 14px; text-align:left;">Score</th>
                        <th style="padding:12px 14px; text-align:left;">Answers</th>
                    </tr>
                </thead>
                <tbody id="studentsBody">
                    <?php if (empty($students)): ?>
                        <tr><td colspan="6" style="padding:16px; color:#b3c6e0; text-align:center;">No submissions yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($students as $stu): ?>
                            <tr class="student-row" data-section="<?= htmlspecialchars($stu['year_section'] ?? '') ?>" data-name="<?= htmlspecialchars(strtolower($stu['student_name'] ?? '')) ?>" data-status="<?= htmlspecialchars($stu['status'] ?? '') ?>" data-flagged="<?= (int)($stu['cheating_flag'] ?? 0) ?>" style="border-top:1px solid #25477a; background:#11224a; color:#fff;">
                                <td style="padding:12px 14px; font-weight:600;"><?= htmlspecialchars($stu['student_name'] ?? 'Unknown') ?></td>
                                <td style="padding:12px 14px; font-weight:700;">
                                    <?php if ($stu['cheating_flag']): ?>
                                        <span style="color:#ffd600;">Yes</span>
                                        <a href="#" onclick="openEvidenceModal('<?= $stu['session_id'] ?>'); return false;" style="color:#ffeb3b; font-weight:700; margin-left:6px; text-decoration:underline;">(See proof)</a>
                                    <?php else: ?>
                                        <span style="color:#22c55e;">No</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:12px 14px;">
                                    <?php if ($stu['status'] === 'completed'): ?>
                                        <span class="status-badge finished">Finished</span>
                                    <?php elseif ($stu['status'] === 'ongoing'): ?>
                                        <span class="status-badge answering">Answering</span>
                                    <?php else: ?>
                                        <span class="status-badge other"><?= htmlspecialchars(ucfirst($stu['status'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:12px 14px;"><?= !empty($stu['completed_at']) ? date('m/d/Y · h:i A', strtotime($stu['completed_at'])) : '-' ?></td>
                                <td style="padding:12px 14px;">
                                    <?php
                                        $started = !empty($stu['started_at']) ? date('h:i A', strtotime($stu['started_at'])) : '—';
                                        $ended = !empty($stu['completed_at']) ? date('h:i A', strtotime($stu['completed_at'])) : '—';
                                        echo $started . ' • ' . $ended;
                                    ?>
                                </td>
                                <td style="padding:12px 14px; color:#ffd600; font-weight:700;">
                                    <?php
                                        $ts = (int)($stu['total_score'] ?? 0);
                                        $tp = (int)$total_points;
                                        echo $ts . ($tp > 0 ? ' / ' . $tp : '');
                                    ?>
                                </td>
                                <td style="padding:12px 14px; text-align:right;">
                                    <button class="review-btn" onclick="openAnswersModal('<?= $stu['session_id'] ?>')">Check answers</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>


        <?php
        
        $completed = array_filter($students, function($s){ return $s['status'] === 'completed'; });
        $totalCompleted = count($completed);
        $sumScores = 0;
        foreach ($completed as $s) { $sumScores += (int)($s['total_score'] ?? 0); }
        $avgScore = $totalCompleted > 0 ? round($sumScores / $totalCompleted) : 0;

        $cheatedCount = array_sum(array_map(function($s){ return (int)($s['cheating_flag'] ?? 0); }, $students));
        $percentCheated = count($students) > 0 ? round(($cheatedCount / count($students)) * 100) : 0;

        $sumMinutes = 0; $countDur = 0;
        foreach ($completed as $s) {
            if (!empty($s['started_at']) && !empty($s['completed_at'])) {
                $start = strtotime($s['started_at']);
                $end = strtotime($s['completed_at']);
                if ($start && $end && $end >= $start) { $sumMinutes += (int)round(($end - $start)/60); $countDur++; }
            }
        }
        $avgFinishMins = $countDur > 0 ? round($sumMinutes / $countDur) : 0;

        $top = $students;
        usort($top, function($a,$b){ return (int)($b['total_score'] ?? 0) <=> (int)($a['total_score'] ?? 0); });
        $top3 = array_slice($top, 0, 3);
        ?>

        <div class="section-divider"></div>
        <div class="analytics-wrap">
            <div class="analytics-left">
                <div class="analytics-title">Student Analytics</div>
                <div class="analytics-row">Average Score : <span class="analytics-number"><?= $avgScore ?></span></div>
                <div class="analytics-row">Percentage of students who cheated : <span class="analytics-number"><?= $percentCheated ?>%</span></div>
                <div class="analytics-row">Average Time for Students To Finish : <span class="analytics-number"><?= $avgFinishMins ?> minutes</span></div>
            </div>
            <div class="analytics-right">
                <div class="analytics-title">Top 3 Scores</div>
                <table class="top-scores-table">
                    <thead>
                        <tr><th>Name</th><th>Score</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top3 as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['student_name'] ?? 'Unknown') ?></td>
                            <td class="score-cell"><?= (int)($row['total_score'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>



        <?php
        $totalStudents = count($students);
        $cheaters = array_values(array_filter($students, function($s){ return (int)($s['cheating_flag'] ?? 0) === 1; }));
        $numCheaters = count($cheaters);
        $numNot = max(0, $totalStudents - $numCheaters);
        $cheatPercent = $totalStudents > 0 ? round(($numCheaters / $totalStudents) * 100) : 0;
        $notPercent = 100 - $cheatPercent;

        $cheatRows = [];
        foreach ($cheaters as $c) {
            $cause = '';
            if (($c['tab_switch_count'] ?? 0) > 0) $cause = 'Switched tabs';
            elseif ((($c['face_left_count'] ?? 0) + ($c['face_right_count'] ?? 0)) > 0) $cause = 'Looked away from the screen';
            elseif (($c['screenshot_count'] ?? 0) > 0) $cause = 'Screenshot detected';
            elseif (($c['suspicious_count'] ?? 0) > 0) $cause = 'Suspicious activity';
            else $cause = 'Exceeded warnings';
            $cheatRows[] = [
                'name' => $c['student_name'] ?? 'Unknown',
                'cause' => $cause,
                'session_id' => $c['session_id']
            ];
        }
        $cheatRows = array_slice($cheatRows, 0, 5);
        $deg = $totalStudents > 0 ? (360 * $numCheaters / $totalStudents) : 0;
        ?>

        <div class="section-divider"></div>
        <div class="cheating-wrap">
            <div class="cheating-left">
                <div class="analytics-title">Cheating vs. Not Cheating</div>
                <div class="pie-card">
                    <div class="pie-chart" style="background: conic-gradient(#0ea5d6 <?= $deg ?>deg, rgba(255,255,255,0.18) 0);"></div>
                    <div class="pie-legend">
                        <div class="legend-item"><span class="dot blue"></span><?= $numCheaters ?> Students cheated</div>
                        <div class="legend-item"><span class="dot gray"></span><?= $numNot ?> Students didn't cheat</div>
                    </div>
                </div>
            </div>
            <div class="cheating-right">
                <div class="analytics-title">Cheating</div>
                <table class="top-scores-table">
                    <thead>
                        <tr><th>Name</th><th>Cause</th><th>Evidence</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cheatRows)): ?>
                            <tr><td colspan="3" style="padding:12px; color:#b3c6e0;">No cheating students.</td></tr>
                        <?php else: foreach ($cheatRows as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['name']) ?></td>
                                <td><?= htmlspecialchars($r['cause']) ?></td>
                                <td><a href="#" onclick="openEvidenceModal('<?= htmlspecialchars($cheaters[array_search($r,$cheatRows)]['session_id'] ?? '') ?>'); return false;" class="evidence-link">See photo</a></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        $missed = [];
        $miss_sql = "SELECT question_id, question_type, question_text, correct_answer, question_points, SUM(CASE WHEN is_correct = 0 THEN 1 ELSE 0 END) AS wrongs
                     FROM student_responses WHERE assessment_id = ? GROUP BY question_id, question_type, question_text, correct_answer, question_points ORDER BY wrongs DESC LIMIT 3";
        $miss_stmt = $conn->prepare($miss_sql);
        $miss_stmt->bind_param("s", $assessment_id);
        $miss_stmt->execute();
        $miss_res = $miss_stmt->get_result();
        while ($mr = $miss_res->fetch_assoc()) { $missed[] = $mr; }

        $mcqOptions = [];
        if (!empty($missed)) {
            $ids = array_map(function($m){ return $m['question_id']; }, $missed);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $types = str_repeat('s', count($ids));
            $q = "SELECT question_id, option_a, option_b, option_c, option_d, option_e FROM multiple_choice_questions WHERE question_id IN ($placeholders)";
            $stmt = $conn->prepare($q);
            $stmt->bind_param($types, ...$ids);
            $stmt->execute();
            $r = $stmt->get_result();
            while ($row = $r->fetch_assoc()) { $mcqOptions[$row['question_id']] = $row; }
        }
        ?>

        <div class="section-divider"></div>
        <div class="missed-wrap">
            <div class="analytics-title">Frequently missed questions</div>
            <?php if (empty($missed)): ?>
                <div class="missed-empty">No missed questions yet.</div>
            <?php else: ?>
                <?php $i=1; foreach ($missed as $m): $qid = $m['question_id']; $opts = $mcqOptions[$qid] ?? []; ?>
                <div class="missed-item">
                    <div class="missed-index"><?= $i ?></div>
                    <div class="missed-content">
                        <div class="missed-question"><?= htmlspecialchars($m['question_text']) ?></div>
                        <?php if (!empty($opts)): ?>
                        <div class="missed-options">
                            <?php
                                $correct = strtoupper(trim($m['correct_answer']));
                                $labels = ['A'=>'option_a','B'=>'option_b','C'=>'option_c','D'=>'option_d','E'=>'option_e'];
                                foreach ($labels as $lbl=>$field) {
                                    if (!empty($opts[$field])) {
                                        $cls = ($lbl === $correct) ? 'opt correct' : 'opt';
                                        echo '<div class="'.$cls.'"><span class="opt-label">'.$lbl.'.</span> '.htmlspecialchars($opts[$field]).'</div>';
                                    }
                                }
                            ?>
                            <div class="missed-points"><?= (int)($m['question_points'] ?? 1) ?> point<?= ((int)$m['question_points']===1?'':'s') ?></div>
                        </div>
                        <?php else: ?>
                        <?php if (($m['question_type'] ?? '') === 'true_false'): ?>
                        <div class="missed-options">
                            <?php
                                $ans = strtoupper(trim($m['correct_answer']));
                                if ($ans === 'TRUE') $ans = 'A';
                                if ($ans === 'FALSE') $ans = 'B';
                                $optAClass = ($ans === 'A') ? 'opt correct' : 'opt';
                                $optBClass = ($ans === 'B') ? 'opt correct' : 'opt';
                                echo '<div class="'.$optAClass.'"><span class="opt-label">A.</span> True</div>';
                                echo '<div class="'.$optBClass.'"><span class="opt-label">B.</span> False</div>';
                            ?>
                            <div class="missed-points"><?= (int)($m['question_points'] ?? 1) ?> point<?= ((int)$m['question_points']===1?'':'s') ?></div>
                        </div>
                        <?php else: ?>
                        <div class="missed-generic">Correct answer: <span class="correct-text"><?= htmlspecialchars($m['correct_answer']) ?></span> • <?= (int)($m['question_points'] ?? 1) ?> point<?= ((int)$m['question_points']===1?'':'s') ?></div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php $i++; endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="section-divider"></div>
        <div class="security-wrap">
            <div class="security-title">Security</div>
            <div class="security-row">
                <div class="security-icon">✖</div>
                <div class="security-text">
                    <div>This data can be tampered with or hacked.</div>
                    <div>Please upload it to the blockchain as soon as possible.</div>
                </div>
            </div>
        </div>

        <div class="actions-wrap">
            <button class="btn btn-yellow" onclick="exportReportPdf()">Export to PDF</button>
            <button class="btn btn-cyan" onclick="uploadToChain()">Upload data to blockchain</button>
            <button class="btn btn-cyan" onclick="syncWithChain()">Sync Data with Blockchain</button>
        </div>
        </div>

    </main>
</div>
<script src="js/sidebar-active.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    try {
        if (typeof setActiveSidebar === 'function') {
            setActiveSidebar('reports');
        } else {
            const reportsLink = document.querySelector('.sidebar a[href="reports.php"]');
            reportsLink?.classList?.add('active');
        }
    } catch(e) {}
});

const sectionFilter = document.getElementById('sectionFilter');
const statusFilter = document.getElementById('statusFilter');
const searchInput = document.getElementById('studentSearch');
function applyFilters() {
    const sec = (sectionFilter?.value || '').toLowerCase();
    const stat = (statusFilter?.value || '').toLowerCase();
    const q = (searchInput?.value || '').toLowerCase();
    document.querySelectorAll('#studentsBody .student-row').forEach(row => {
        const rowSec = (row.dataset.section || '').toLowerCase();
        const rowName = (row.dataset.name || '').toLowerCase();
        const rowStatus = (row.dataset.status || '').toLowerCase();
        const rowFlagged = (row.dataset.flagged || '0') === '1';
        let show = true;
        if (sec && rowSec !== sec) show = false;
        if (stat) {
            if (stat === 'answering' && rowStatus !== 'ongoing') show = false;
            if (stat === 'finished' && rowStatus !== 'completed') show = false;
            if (stat === 'flagged' && !rowFlagged) show = false;
        }
        if (q && !rowName.includes(q)) show = false;
        row.style.display = show ? '' : 'none';
    });
}
sectionFilter?.addEventListener('change', applyFilters);
statusFilter?.addEventListener('change', applyFilters);
searchInput?.addEventListener('input', applyFilters);
</script>
<script>
function exportReportPdf(){
    const element = document.getElementById('reportContent');
    if (!element) { alert('Content not found'); return; }
    const cloned = element.cloneNode(true);
    const actions = cloned.querySelector('.actions-wrap');
    if (actions) actions.remove();
    const sectionFilterEl = cloned.querySelector('#sectionFilter');
    if (sectionFilterEl) {
        const filterRow = sectionFilterEl.closest('.filters-row');
        if (filterRow) filterRow.remove();
    }
    const bg = getComputedStyle(document.querySelector('.main-content')).background || getComputedStyle(document.body).background || '#0a2240';
    cloned.style.background = bg;
    const wrapper = document.createElement('div');
    wrapper.style.padding = '16px';
    wrapper.appendChild(cloned);
    document.body.appendChild(wrapper);
    const opt = {
        margin:       [6,6,6,6],
        filename:     `assessment-report-${Date.now()}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true, scrollY: 0, windowWidth: document.documentElement.scrollWidth, backgroundColor: getComputedStyle(document.body).backgroundColor || '#0a2240' },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(wrapper).save().then(()=>{ document.body.removeChild(wrapper); });
}
function uploadToChain(){ alert('Upload to blockchain will be implemented.'); }
function syncWithChain(){ alert('Sync with blockchain will be implemented.'); }
async function openAnswersModal(sessionId){
    try {
        const modal = document.getElementById('answersModal');
        modal.style.display = 'block';
        document.getElementById('answersList').innerHTML = '<div class="no-evidence">Loading...</div>';

        const res = await fetch(`phpfiles/get_student_answers.php?session_id=${sessionId}`);
        const data = await res.json();
        if (!data.success) { document.getElementById('answersList').innerHTML = '<div class="no-evidence">'+(data.message||'Error')+'</div>'; return; }

        const sess = data.session; const sum = data.summary;
        document.getElementById('ansName').textContent = sess.student_name || 'Unknown';
        document.getElementById('ansSection').textContent = sess.year_section || 'N/A';
        document.getElementById('ansStarted').textContent = formatDateTime(sess.started_at);
        document.getElementById('ansCompleted').textContent = sess.completed_at ? formatDateTime(sess.completed_at) : '—';
        document.getElementById('ansScore').textContent = `${sum.score_earned} / ${sum.score_total}`;
        document.getElementById('ansCount').textContent = sum.answers_count;
        const mins = sum.time_spent_seconds ? Math.round(sum.time_spent_seconds/60) : 0;
        document.getElementById('ansTime').textContent = `${mins} minutes`;

        const list = document.getElementById('answersList');
        list.innerHTML = '';
        const items = (data.answers||[]);
        updateAnswersChips(items);
        const groups = { 'multiple_choice': [], 'identification': [], 'true_false': [] };
        items.forEach(a=>{ (groups[a.question_type] = groups[a.question_type] || []).push(a); });
        const order = ['multiple_choice','identification','true_false'];
        let idx = 0;
        order.forEach(type => {
            const arr = groups[type] || [];
            if (arr.length === 0) return;
            const title = document.createElement('div');
            title.className = 'answers-group-title';
            title.textContent = type.replace('_',' ').replace('_',' ').replace(/\b\w/g, c=>c.toUpperCase());
            list.appendChild(title);
            arr.forEach(a => {
                idx++;
                const item = document.createElement('div');
                item.className = 'answer-item';
                const correct = (a.is_correct == 1) || (String(a.student_answer).trim().toLowerCase() === String(a.correct_answer).trim().toLowerCase());
                const studentPartClass = 'answer-part ' + (correct ? 'correct' : 'incorrect');
                item.dataset.correct = correct ? '1' : '0';
                item.dataset.q = `${a.question_text||''} ${a.student_answer||''} ${a.correct_answer||''}`.toLowerCase();
                item.innerHTML = `
                    <div class=\"answer-head\"><div class=\"question-title\">${idx})  ${a.question_text || ''}</div><div class=\"answer-time\">${formatDateTime(a.submitted_at||sess.started_at)}</div></div>
                    <div class=\"answer-body\">                        
                        <div class=\"${studentPartClass}\"><strong>Your answer:</strong> ${a.student_answer || ''}</div>
                        <div class=\"answer-part correct\"><strong>Correct answer:</strong> ${a.correct_answer || ''}</div>
                        <div class=\"answer-part\"><strong>Points:</strong> ${a.question_points || 0}</div>
                    </div>`;
                list.appendChild(item);
            });
        });

    } catch(err){
        document.getElementById('answersList').innerHTML = '<div class="no-evidence">Error loading answers</div>';
    }
}
function closeAnswersModal(){ document.getElementById('answersModal').style.display = 'none'; }
function updateAnswersChips(items){
    document.getElementById('ansTotalChip').textContent = `${items.length} Qs`;
    const c = items.filter(a=> (a.is_correct==1) || (String(a.student_answer).trim().toLowerCase()===String(a.correct_answer).trim().toLowerCase())).length;
    document.getElementById('ansCorrectChip').textContent = `${c} Correct`;
    document.getElementById('ansIncorrectChip').textContent = `${items.length-c} Incorrect`;
}
document.addEventListener('DOMContentLoaded',()=>{
    const onlyInc = document.getElementById('onlyIncorrect');
    const search = document.getElementById('ansSearch');
    function applyAnsFilters(){
        const showOnlyIncorrect = !!onlyInc?.checked; const q = (search?.value||'').toLowerCase();
        const items = document.querySelectorAll('#answersList .answer-item');
        items.forEach(it=>{
            let show = true;
            if (showOnlyIncorrect && it.dataset.correct==='1') show = false;
            if (q && !(it.dataset.q||'').includes(q)) show = false;
            it.style.display = show ? '' : 'none';
        });
    }
    onlyInc?.addEventListener('change', applyAnsFilters);
    search?.addEventListener('input', applyAnsFilters);
});

let currentEvidence = [];
let currentEvidenceIndex = 0;
let filteredEvidence = [];

async function openEvidenceModal(sessionId){
    try {
        document.getElementById('reviewModal').style.display = 'block';
        document.getElementById('evidenceCarousel').innerHTML = '<div class="no-evidence">Loading evidence...</div>';
        document.getElementById('studentDetails').innerHTML = '<div>Loading student details...</div>';

        const response = await fetch(`phpfiles/get_cheating_evidence.php?session_id=${sessionId}`);
        if (!response.ok) throw new Error(`HTTP error ${response.status}`);
        const data = await response.json();
        if (data.success) {
            currentEvidence = data.evidence;
            filteredEvidence = [...currentEvidence];
            currentEvidenceIndex = 0;
            displayStudentDetails(data.student);
            displayEvidence();
            updateCarouselIndicators();
        } else {
            document.getElementById('evidenceCarousel').innerHTML = '<div class="no-evidence">Error: '+data.message+'</div>';
            document.getElementById('studentDetails').innerHTML = '<div>Error loading student details</div>';
        }
    } catch(err){
        document.getElementById('evidenceCarousel').innerHTML = '<div class="no-evidence">Error loading evidence</div>';
        document.getElementById('studentDetails').innerHTML = '<div>Error loading student details</div>';
    }
}

function closeReviewModal(){ document.getElementById('reviewModal').style.display = 'none'; }

function displayStudentDetails(student){
    const studentDetails = document.getElementById('studentDetails');
    studentDetails.innerHTML = `
        <div class="detail-row"><span class="detail-label">Name:</span><span class="detail-value">${student.student_name}</span></div>
        <div class="detail-row"><span class="detail-label">Section:</span><span class="detail-value">${student.year_section || 'N/A'}</span></div>
        <div class="detail-row"><span class="detail-label">Started:</span><span class="detail-value">${formatDateTime(student.started_at)}</span></div>
        <div class="detail-row"><span class="detail-label">Status:</span><span class="detail-value">${student.status}</span></div>
        <div class="detail-row"><span class="detail-label">Total Warnings:</span><span class="detail-value">${student.total_warnings}</span></div>
    `;
}

function displayEvidence(){
    const carousel = document.getElementById('evidenceCarousel');
    if (filteredEvidence.length === 0) { carousel.innerHTML = '<div class="no-evidence">No evidence found</div>'; return; }
    const currentItem = filteredEvidence[currentEvidenceIndex];
    const eventTypeLabels = { 'TAB_SWITCH':'Tab Switching','FACE_LEFT':'Face Left','FACE_RIGHT':'Face Right' };
    const severityColors = { 'LOW':'#4CAF50','MEDIUM':'#FF9800','HIGH':'#F44336' };
    carousel.innerHTML = `
        <div class="evidence-item">
            <div class="evidence-header-info">
                <h4>${eventTypeLabels[currentItem.event_type] || currentItem.event_type}</h4>
                <span class="severity-badge" style="background-color: ${severityColors[currentItem.severity]}">${currentItem.severity}</span>
            </div>
            <div class="evidence-time"><strong>Detected:</strong> ${formatDateTime(currentItem.event_time)}</div>
            ${currentItem.screenshot_url ? `
                <div class="evidence-screenshot">
                    <img src="${currentItem.screenshot_url}" alt="Evidence Screenshot" onclick="openImageModal('${currentItem.screenshot_url}','${eventTypeLabels[currentItem.event_type] || currentItem.event_type}')" class="clickable-image" />
                </div>
            ` : `
                <div class="no-screenshot"><p>No screenshot available for this event</p></div>
            `}
        </div>`;
}

function updateCarouselIndicators(){
    const indicators = document.getElementById('carouselIndicators');
    indicators.innerHTML = '';
    for (let i=0;i<filteredEvidence.length;i++){
        const indicator = document.createElement('span');
        indicator.className = `indicator ${i === currentEvidenceIndex ? 'active' : ''}`;
        indicator.onclick = () => { currentEvidenceIndex = i; displayEvidence(); updateCarouselIndicators(); };
        indicators.appendChild(indicator);
    }
}

function previousEvidence(){ if (filteredEvidence.length===0) return; currentEvidenceIndex = (currentEvidenceIndex - 1 + filteredEvidence.length) % filteredEvidence.length; displayEvidence(); updateCarouselIndicators(); }
function nextEvidence(){ if (filteredEvidence.length===0) return; currentEvidenceIndex = (currentEvidenceIndex + 1) % filteredEvidence.length; displayEvidence(); updateCarouselIndicators(); }
function openImageModal(src,title){ document.getElementById('enlargedImage').src = src; document.getElementById('imageModalTitle').textContent = `${title} - Evidence Screenshot`; document.getElementById('imageModal').style.display = 'block'; }
function closeImageModal(){ document.getElementById('imageModal').style.display = 'none'; }
function formatDateTime(ts){ const d = new Date(ts); return d.toLocaleString('en-US',{year:'numeric',month:'2-digit',day:'2-digit',hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:true}); }

document.addEventListener('DOMContentLoaded', function(){
    const evidenceFilter = document.getElementById('evidenceFilter');
    if (evidenceFilter) {
        evidenceFilter.addEventListener('change', function(){
            const v = this.value;
            if (v === 'all') filteredEvidence = [...currentEvidence]; else filteredEvidence = currentEvidence.filter(it => it.event_type === v);
            currentEvidenceIndex = 0; displayEvidence(); updateCarouselIndicators();
        });
    }
});
</script>
</script>
</body>
</html>


