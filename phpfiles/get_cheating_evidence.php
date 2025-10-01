<?php
require_once '../auth/check_session.php';
require_once '../database/db_config.php';

header('Content-Type: application/json');

if (!isset($_GET['session_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session ID is required']);
    exit();
}

$session_id = $_GET['session_id'];
$owner_id = $_SESSION['unique_id'];

try {
    // First verify that the session belongs to an assessment owned by the logged-in user
    $verify_sql = "SELECT s.session_id, s.student_name, s.year_section, s.started_at, s.status, 
                          s.cheating_flag, s.cheating_reason, ca.owner_id
                   FROM assessment_sessions s
                   JOIN created_assessments ca ON s.assessment_id = ca.unique_id
                   WHERE s.session_id = ? AND ca.owner_id = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("ss", $session_id, $owner_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Session not found or access denied']);
        exit();
    }
    
    $student = $verify_result->fetch_assoc();
    
    // Get all cheating events for this session and count them
    $evidence_sql = "SELECT event_type, severity, event_time, meta, screenshot_url, thumbnail_url, checksum
                     FROM cheating_events 
                     WHERE session_id = ? 
                     ORDER BY event_time ASC";
    $evidence_stmt = $conn->prepare($evidence_sql);
    $evidence_stmt->bind_param("s", $session_id);
    $evidence_stmt->execute();
    $evidence_result = $evidence_stmt->get_result();
    
    $evidence = [];
    $total_warnings = 0;
    while ($row = $evidence_result->fetch_assoc()) {
        $evidence[] = $row;
        $total_warnings++;
    }
    
    // Add total_warnings to student data
    $student['total_warnings'] = $total_warnings;
    
    echo json_encode([
        'success' => true,
        'student' => $student,
        'evidence' => $evidence
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching cheating evidence: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching evidence']);
}
?>
