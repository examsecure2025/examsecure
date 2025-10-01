<?php
require_once '../auth/check_session.php';
require_once '../database/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['question_id']) || !isset($input['question_type'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$question_id = $input['question_id'];
$question_type = $input['question_type'];
$owner_id = $_SESSION['unique_id'];

// First, get the assessment_id for this question to verify ownership
$assessment_id = null;
switch ($question_type) {
    case 'mcq':
        $sql = "SELECT q.assessment_id FROM multiple_choice_questions q 
                JOIN created_assessments a ON q.assessment_id = a.unique_id 
                WHERE q.question_id = ? AND a.owner_id = ?";
        break;
    case 'id':
        $sql = "SELECT q.assessment_id FROM identification_questions q 
                JOIN created_assessments a ON q.assessment_id = a.unique_id 
                WHERE q.question_id = ? AND a.owner_id = ?";
        break;
    case 'tf':
        $sql = "SELECT q.assessment_id FROM true_false_questions q 
                JOIN created_assessments a ON q.assessment_id = a.unique_id 
                WHERE q.question_id = ? AND a.owner_id = ?";
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid question type']);
        exit();
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $question_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Question not found or access denied']);
    exit();
}

$row = $result->fetch_assoc();
$assessment_id = $row['assessment_id'];

// Now delete the question
try {
    switch ($question_type) {
        case 'mcq':
            $sql = "DELETE FROM multiple_choice_questions WHERE question_id = ? AND assessment_id = ?";
            break;
        case 'id':
            $sql = "DELETE FROM identification_questions WHERE question_id = ? AND assessment_id = ?";
            break;
        case 'tf':
            $sql = "DELETE FROM true_false_questions WHERE question_id = ? AND assessment_id = ?";
            break;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $question_id, $assessment_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Question deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete question']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error deleting question: ' . $e->getMessage()]);
}
?>
