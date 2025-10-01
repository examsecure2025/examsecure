<?php
// Prevent any output before headers
ob_start();

require_once '../auth/check_session.php';
require_once '../database/db_config.php';

// Clear any output buffer
ob_clean();

header('Content-Type: application/json');

// Debug logging
error_log('update_shuffle_settings.php called');

// Check database connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

// Debug logging
error_log('Received input: ' . print_r($input, true));

if (!isset($input['assessment_id']) || !isset($input['question_type']) || !isset($input['shuffle_value'])) {
    error_log('Missing parameters: ' . print_r($input, true));
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$assessment_id = $input['assessment_id'];
$question_type = $input['question_type'];
$shuffle_value = $input['shuffle_value'];
$owner_id = $_SESSION['unique_id'];

// Debug logging
error_log("Session data: " . print_r($_SESSION, true));
error_log("Processing: assessment_id=$assessment_id, question_type=$question_type, shuffle_value=$shuffle_value, owner_id=$owner_id");

try {
    // Validate question type
    if (!in_array($question_type, ['mcq', 'id', 'tf'])) {
        error_log("Invalid question type: $question_type");
        echo json_encode(['success' => false, 'message' => 'Invalid question type: ' . $question_type]);
        exit();
    }
    
    error_log("Processing shuffle update for question type: $question_type");
    
    // First, let's check what's in the database for this assessment
    $debug_sql = "SELECT unique_id, owner_id, shuffle_mcq, shuffle_identification, shuffle_true_false FROM created_assessments WHERE unique_id = ?";
    $debug_stmt = $conn->prepare($debug_sql);
    $debug_stmt->bind_param("s", $assessment_id);
    $debug_stmt->execute();
    $debug_result = $debug_stmt->get_result();
    
    if ($debug_result->num_rows === 0) {
        error_log("Assessment not found in database: assessment_id=$assessment_id");
        echo json_encode(['success' => false, 'message' => 'Assessment not found in database']);
        $debug_stmt->close();
        exit();
    }
    
    $debug_row = $debug_result->fetch_assoc();
    error_log("Database row found: " . print_r($debug_row, true));
    $debug_stmt->close();
    
    // Check if the assessment exists and belongs to the user
    $check_assessment_sql = "SELECT unique_id FROM created_assessments WHERE unique_id = ? AND owner_id = ?";
    $check_assessment_stmt = $conn->prepare($check_assessment_sql);
    $check_assessment_stmt->bind_param("ss", $assessment_id, $owner_id);
    $check_assessment_stmt->execute();
    $assessment_result = $check_assessment_stmt->get_result();
    
    if ($assessment_result->num_rows === 0) {
        error_log("Assessment not found or access denied: assessment_id=$assessment_id, owner_id=$owner_id");
        echo json_encode(['success' => false, 'message' => 'Assessment not found or access denied']);
        $check_assessment_stmt->close();
        exit();
    }
    
    $check_assessment_stmt->close();
    
    // Update the shuffle setting based on question type
    $stmt = null;
    $success = false;
    $column_name = '';
    
    switch ($question_type) {
        case 'mcq':
            $column_name = 'shuffle_mcq';
            $sql = "UPDATE created_assessments SET $column_name = ? WHERE unique_id = ? AND owner_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $shuffle_value, $assessment_id, $owner_id);
            break;
            
        case 'id':
            $column_name = 'shuffle_identification';
            $sql = "UPDATE created_assessments SET $column_name = ? WHERE unique_id = ? AND owner_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $shuffle_value, $assessment_id, $owner_id);
            break;
            
        case 'tf':
            $column_name = 'shuffle_true_false';
            $sql = "UPDATE created_assessments SET $column_name = ? WHERE unique_id = ? AND owner_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $shuffle_value, $assessment_id, $owner_id);
            break;
    }
    
    if ($stmt) {
        error_log("SQL: $sql");
        error_log("Parameters: shuffle_value=$shuffle_value, assessment_id=$assessment_id, owner_id=$owner_id");
        error_log("Parameter types: shuffle_value=" . gettype($shuffle_value) . ", assessment_id=" . gettype($assessment_id) . ", owner_id=" . gettype($owner_id));
        
        if ($stmt->execute()) {
            error_log("SQL executed successfully. Affected rows: " . $stmt->affected_rows);
            if ($stmt->affected_rows > 0) {
                $success = true;
                
                // Verify the update actually happened
                $verify_sql = "SELECT $column_name FROM created_assessments WHERE unique_id = ?";
                $verify_stmt = $conn->prepare($verify_sql);
                $verify_stmt->bind_param("s", $assessment_id);
                $verify_stmt->execute();
                $verify_result = $verify_stmt->get_result();
                $row = $verify_result->fetch_assoc();
                
                if ($row && $row[$column_name] == $shuffle_value) {
                    error_log("Verification successful: column $column_name = " . $row[$column_name]);
                    echo json_encode(['success' => true, 'message' => 'Shuffle setting updated successfully']);
                } else {
                    error_log("Verification failed: expected $shuffle_value, got " . ($row ? $row[$column_name] : 'NULL'));
                    echo json_encode(['success' => false, 'message' => 'Update verification failed']);
                }
                $verify_stmt->close();
            } else {
                error_log("No rows were affected - possible issue with WHERE clause");
                error_log("Let's check if the WHERE clause would match any rows:");
                
                // Debug: Check if the WHERE clause would match any rows
                $debug_where_sql = "SELECT COUNT(*) as count FROM created_assessments WHERE unique_id = ? AND owner_id = ?";
                $debug_where_stmt = $conn->prepare($debug_where_sql);
                $debug_where_stmt->bind_param("ss", $assessment_id, $owner_id);
                $debug_where_stmt->execute();
                $debug_where_result = $debug_where_stmt->get_result();
                $debug_where_row = $debug_where_result->fetch_assoc();
                error_log("Rows matching WHERE clause: " . $debug_where_row['count']);
                $debug_where_stmt->close();
                
                echo json_encode(['success' => false, 'message' => 'No rows were updated - check assessment ID and ownership']);
            }
        } else {
            error_log("SQL execution failed: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Failed to update shuffle setting: ' . $stmt->error]);
        }
    } else {
        error_log("Failed to prepare statement for question type: $question_type");
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
    }
    
} catch (Exception $e) {
    error_log("Exception caught: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating shuffle setting: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
