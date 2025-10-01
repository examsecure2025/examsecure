<?php
require_once '../auth/check_session.php';
require_once '../database/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['assessment_id'])) {
    echo json_encode(['success' => false, 'message' => 'Assessment ID is required']);
    exit();
}

$assessment_id = $input['assessment_id'];
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
    
    // Update cheating_flag for students with >3 warnings
    $update_sql = "UPDATE assessment_sessions 
                   SET cheating_flag = 1, 
                       cheating_reason = CASE 
                           WHEN cheating_reason IS NULL OR cheating_reason = '' THEN 'Exceeded warning threshold (>3 warnings)'
                           ELSE cheating_reason
                       END
                   WHERE assessment_id = ? 
                   AND (tab_switch_count + face_left_count + face_right_count) > 3
                   AND cheating_flag = 0";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("s", $assessment_id);
    $update_stmt->execute();
    
    $affected_rows = $update_stmt->affected_rows;
    
    // Also set cheating_flag to 0 for students with <=3 warnings
    $reset_sql = "UPDATE assessment_sessions 
                  SET cheating_flag = 0, 
                      cheating_reason = NULL
                  WHERE assessment_id = ? 
                  AND (tab_switch_count + face_left_count + face_right_count) <= 3
                  AND cheating_flag = 1";
    
    $reset_stmt = $conn->prepare($reset_sql);
    $reset_stmt->bind_param("s", $assessment_id);
    $reset_stmt->execute();
    
    $reset_rows = $reset_stmt->affected_rows;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Cheating flags updated successfully',
        'flagged_students' => $affected_rows,
        'unflagged_students' => $reset_rows
    ]);
    
} catch (Exception $e) {
    error_log("Error updating cheating flags: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
?>
