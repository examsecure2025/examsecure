<?php
// Prevent any output before headers
ob_start();

require_once '../auth/check_session.php';
require_once '../database/db_config.php';

// Clear any output buffer
ob_clean();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

// Debug: Log the received data
error_log('Received input: ' . print_r($input, true));

if (!isset($input['assessment_id']) || !isset($input['question_type']) || !isset($input['questions'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$assessment_id = $input['assessment_id'];
$question_type = $input['question_type'];
$questions = $input['questions'];
$deleted_question_ids = isset($input['deleted_question_ids']) && is_array($input['deleted_question_ids']) ? $input['deleted_question_ids'] : [];
$owner_id = $_SESSION['unique_id'];

// Verify the assessment belongs to the current user
$sql = "SELECT unique_id FROM created_assessments WHERE unique_id = ? AND owner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $assessment_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Assessment not found or access denied']);
    exit();
}

$conn->begin_transaction();

try {
    switch ($question_type) {
        case 'mcq':
            // Handle deletions first
            if (!empty($deleted_question_ids)) {
                $placeholders = implode(',', array_fill(0, count($deleted_question_ids), '?'));
                $types = str_repeat('s', count($deleted_question_ids)) . 's';
                $sql = "DELETE FROM multiple_choice_questions WHERE assessment_id = ? AND question_id IN ($placeholders)";
                $stmt = $conn->prepare($sql);
                $params = array_merge([$assessment_id], $deleted_question_ids);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
            }
            foreach ($questions as $question) {
                // Basic validation & normalization
                $qt = isset($question['question_text']) ? trim((string)$question['question_text']) : '';
                $pa = isset($question['option_a']) ? trim((string)$question['option_a']) : '';
                $pb = isset($question['option_b']) ? trim((string)$question['option_b']) : '';
                $pc = isset($question['option_c']) ? trim((string)$question['option_c']) : '';
                $pd = isset($question['option_d']) ? trim((string)$question['option_d']) : '';
                $pe = isset($question['option_e']) ? trim((string)$question['option_e']) : '';
                $ca = isset($question['correct_answer']) ? trim((string)$question['correct_answer']) : '';
                $pts = (string)($question['points'] ?? '1');

                // Require non-empty question and at least 3 options
                $provided = ['A' => $pa, 'B' => $pb, 'C' => $pc, 'D' => $pd, 'E' => $pe];
                $nonEmptyCount = 0; $validLetters = [];
                foreach ($provided as $letter => $val) {
                    if ($val !== '') { $nonEmptyCount++; $validLetters[] = $letter; }
                }
                if ($qt === '' || $nonEmptyCount < 3 || $ca === '' || !in_array($ca, $validLetters, true)) {
                    // Skip invalid/empty drafts
                    continue;
                }

                $qid = isset($question['question_id']) ? trim((string)$question['question_id']) : '';
                if ($qid === '') {
                    // INSERT new MCQ
                    $new_id = bin2hex(random_bytes(16));
                    $sql = "INSERT INTO multiple_choice_questions (
                                question_id, assessment_id, question_text, option_a, option_b, option_c, option_d, option_e, correct_answer, points
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                        "sssssssssi",
                        $new_id,
                        $assessment_id,
                        $qt,
                        $pa,
                        $pb,
                        $pc,
                        $pd,
                        $pe,
                        $ca,
                        $pts
                    );
                    $stmt->execute();
                } else {
                    // UPDATE existing MCQ
                    $sql = "UPDATE multiple_choice_questions SET 
                            question_text = ?, option_a = ?, option_b = ?, option_c = ?, 
                            option_d = ?, option_e = ?, correct_answer = ?, points = ? 
                            WHERE question_id = ? AND assessment_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssssssss", 
                        $qt, 
                        $pa, 
                        $pb, 
                        $pc, 
                        $pd, 
                        $pe, 
                        $ca, 
                        $pts, 
                        $qid, 
                        $assessment_id
                    );
                    $stmt->execute();
                }
            }
            
            // Update shuffle settings if provided
            if (isset($input['shuffle_settings']) && isset($input['shuffle_settings']['shuffle_mcq'])) {
                $shuffle_sql = "UPDATE created_assessments SET shuffle_mcq = ? WHERE unique_id = ? AND owner_id = ?";
                $shuffle_stmt = $conn->prepare($shuffle_sql);
                $shuffle_stmt->bind_param("iss", $input['shuffle_settings']['shuffle_mcq'], $assessment_id, $owner_id);
                $shuffle_stmt->execute();
                error_log("Updated shuffle_mcq to: " . $input['shuffle_settings']['shuffle_mcq']);
            }
            break;
            
        case 'id':
            if (!empty($deleted_question_ids)) {
                $placeholders = implode(',', array_fill(0, count($deleted_question_ids), '?'));
                $types = str_repeat('s', count($deleted_question_ids)) . 's';
                $sql = "DELETE FROM identification_questions WHERE assessment_id = ? AND question_id IN ($placeholders)";
                $stmt = $conn->prepare($sql);
                $params = array_merge([$assessment_id], $deleted_question_ids);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
            }
            foreach ($questions as $question) {
                $qt = isset($question['question_text']) ? trim((string)$question['question_text']) : '';
                $ca = isset($question['correct_answer']) ? trim((string)$question['correct_answer']) : '';
                $pts = (string)($question['points'] ?? '1');
                if ($qt === '' || $ca === '') {
                    continue;
                }
                $qid = isset($question['question_id']) ? trim((string)$question['question_id']) : '';
                if ($qid === '') {
                    // INSERT new Identification
                    $new_id = bin2hex(random_bytes(16));
                    $sql = "INSERT INTO identification_questions (
                                question_id, assessment_id, question_text, correct_answer, points
                            ) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                        "ssssi",
                        $new_id,
                        $assessment_id,
                        $qt,
                        $ca,
                        $pts
                    );
                    $stmt->execute();
                } else {
                    // UPDATE existing Identification
                    $sql = "UPDATE identification_questions SET 
                            question_text = ?, correct_answer = ?, points = ? 
                            WHERE question_id = ? AND assessment_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssss", 
                        $qt, 
                        $ca, 
                        $pts, 
                        $qid, 
                        $assessment_id
                    );
                    $stmt->execute();
                }
            }
            
            // Update shuffle settings if provided
            if (isset($input['shuffle_settings']) && isset($input['shuffle_settings']['shuffle_identification'])) {
                $shuffle_sql = "UPDATE created_assessments SET shuffle_identification = ? WHERE unique_id = ? AND owner_id = ?";
                $shuffle_stmt = $conn->prepare($shuffle_sql);
                $shuffle_stmt->bind_param("iss", $input['shuffle_settings']['shuffle_identification'], $assessment_id, $owner_id);
                $shuffle_stmt->execute();
                error_log("Updated shuffle_identification to: " . $input['shuffle_settings']['shuffle_identification']);
            }
            
            // Update AI check setting if provided
            if (isset($input['shuffle_settings']) && isset($input['shuffle_settings']['ai_check_identification'])) {
                $ai_check_sql = "UPDATE created_assessments SET ai_check_identification = ? WHERE unique_id = ? AND owner_id = ?";
                $ai_check_stmt = $conn->prepare($ai_check_sql);
                $ai_check_stmt->bind_param("iss", $input['shuffle_settings']['ai_check_identification'], $assessment_id, $owner_id);
                $ai_check_stmt->execute();
                error_log("Updated ai_check_identification to: " . $input['shuffle_settings']['ai_check_identification']);
            }
            break;
            
        case 'tf':
            if (!empty($deleted_question_ids)) {
                $placeholders = implode(',', array_fill(0, count($deleted_question_ids), '?'));
                $types = str_repeat('s', count($deleted_question_ids)) . 's';
                $sql = "DELETE FROM true_false_questions WHERE assessment_id = ? AND question_id IN ($placeholders)";
                $stmt = $conn->prepare($sql);
                $params = array_merge([$assessment_id], $deleted_question_ids);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
            }
            foreach ($questions as $question) {
                $qt = isset($question['question_text']) ? trim((string)$question['question_text']) : '';
                $ca = isset($question['correct_answer']) ? (string)$question['correct_answer'] : '';
                $pts = (string)($question['points'] ?? '1');
                if ($qt === '' || !in_array($ca, ['0','1',0,1], true)) {
                    continue;
                }
                $qid = isset($question['question_id']) ? trim((string)$question['question_id']) : '';
                if ($qid === '') {
                    // INSERT new True/False
                    $new_id = bin2hex(random_bytes(16));
                    $sql = "INSERT INTO true_false_questions (
                                question_id, assessment_id, question_text, correct_answer, points
                            ) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                        "ssssi",
                        $new_id,
                        $assessment_id,
                        $qt,
                        $ca,
                        $pts
                    );
                    $stmt->execute();
                } else {
                    // UPDATE existing True/False
                    $sql = "UPDATE true_false_questions SET 
                            question_text = ?, correct_answer = ?, points = ? 
                            WHERE question_id = ? AND assessment_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssss", 
                        $qt, 
                        $ca, 
                        $pts, 
                        $qid, 
                        $assessment_id
                    );
                    $stmt->execute();
                }
            }
            
            // Update shuffle settings if provided
            if (isset($input['shuffle_settings']) && isset($input['shuffle_settings']['shuffle_true_false'])) {
                $shuffle_sql = "UPDATE created_assessments SET shuffle_true_false = ? WHERE unique_id = ? AND owner_id = ?";
                $shuffle_stmt = $conn->prepare($shuffle_sql);
                $shuffle_stmt->bind_param("iss", $input['shuffle_settings']['shuffle_true_false'], $assessment_id, $owner_id);
                $shuffle_stmt->execute();
                error_log("Updated shuffle_true_false to: " . $input['shuffle_settings']['shuffle_true_false']);
            }
            break;
            
        default:
            throw new Exception('Invalid question type');
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Questions updated successfully']);
    
} catch (Exception $e) {
    $conn->rollback();
    error_log('Error updating questions: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating questions: ' . $e->getMessage()]);
}
?>
