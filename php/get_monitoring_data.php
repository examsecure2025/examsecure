<?php
require_once '../auth/check_session.php';
require_once '../database/db_config.php';

header('Content-Type: application/json');

if (!isset($_GET['assessment_id'])) {
    echo json_encode(['success' => false, 'message' => 'Assessment ID is required']);
    exit();
}

$assessment_id = $_GET['assessment_id'];
$owner_id = $_SESSION['unique_id'];

try {
    // First verify that the assessment belongs to the logged-in user
    $verify_sql = "SELECT unique_id FROM created_assessments WHERE unique_id = ? AND owner_id = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("ss", $assessment_id, $owner_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Assessment not found or access denied']);
        exit();
    }
    
    // Get all student sessions for this assessment with their latest cheating events
    $sql = "
        SELECT 
            s.session_id,
            s.student_id,
            s.student_name,
            s.year_section,
            s.started_at,
            s.completed_at,
            s.status,
            s.tab_switch_count,
            s.face_left_count,
            s.face_right_count,
            s.suspicious_count,
            s.screenshot_count,
            s.cheating_flag,
            s.cheating_reason,
            (s.tab_switch_count + s.face_left_count + s.face_right_count) as total_warnings,
            sr.total_score,
            ce.recent_event_type,
            ce.recent_event_time
        FROM assessment_sessions s
        LEFT JOIN (
            SELECT 
                session_id,
                SUM(points_earned) as total_score
            FROM student_responses 
            WHERE assessment_id = ?
            GROUP BY session_id
        ) sr ON s.session_id = sr.session_id
        LEFT JOIN (
            SELECT 
                session_id,
                event_type as recent_event_type,
                event_time as recent_event_time,
                ROW_NUMBER() OVER (PARTITION BY session_id ORDER BY event_time DESC) as rn
            FROM cheating_events 
            WHERE session_id IN (
                SELECT session_id FROM assessment_sessions WHERE assessment_id = ?
            )
        ) ce ON s.session_id = ce.session_id AND ce.rn = 1
        WHERE s.assessment_id = ?
        ORDER BY s.started_at DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $assessment_id, $assessment_id, $assessment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $students = [];
    
    while ($row = $result->fetch_assoc()) {
        // Format the student data
        $student = [
            'session_id' => $row['session_id'],
            'student_id' => $row['student_id'],
            'student_name' => $row['student_name'],
            'year_section' => $row['year_section'],
            'started_at' => $row['started_at'],
            'completed_at' => $row['completed_at'],
            'status' => $row['status'],
            'cheating_flag' => (bool)$row['cheating_flag'],
            'cheating_reason' => $row['cheating_reason'],
            'total_warnings' => (int)$row['total_warnings'],
            'score' => $row['total_score'] ? (int)$row['total_score'] : 0,
            'recent_warning' => $row['recent_event_type'],
            'recent_warning_time' => $row['recent_event_time'],
            'warning_breakdown' => [
                'tab_switches' => (int)$row['tab_switch_count'],
                'face_left' => (int)$row['face_left_count'],
                'face_right' => (int)$row['face_right_count'],
                'suspicious_activity' => (int)$row['suspicious_count'],
                'screenshots' => (int)$row['screenshot_count']
            ]
        ];
        
        $students[] = $student;
    }
    
    echo json_encode([
        'success' => true,
        'students' => $students,
        'total_count' => count($students)
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_monitoring_data.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
?>
