<?php
require_once '../auth/check_session.php';
require_once '../database/db_config.php';

header('Content-Type: application/json');

if (!isset($_GET['type']) || !isset($_GET['assessment_id'])) {
    echo json_encode(['error' => 'Missing parameters']);
    exit();
}

$type = $_GET['type'];
$assessment_id = $_GET['assessment_id'];
$owner_id = $_SESSION['unique_id'];

// Verify the assessment belongs to the current user
$sql = "SELECT unique_id FROM created_assessments WHERE unique_id = ? AND owner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $assessment_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Assessment not found or access denied']);
    exit();
}

$questions = [];

switch ($type) {
    case 'mcq':
        $sql = "SELECT question_id, question_text, option_a, option_b, option_c, option_d, option_e, correct_answer, points FROM multiple_choice_questions WHERE assessment_id = ? ORDER BY created_at ASC";
        break;
    case 'id':
        $sql = "SELECT question_id, question_text, correct_answer, points FROM identification_questions WHERE assessment_id = ? ORDER BY created_at ASC";
        break;
    case 'tf':
        $sql = "SELECT question_id, question_text, correct_answer, points FROM true_false_questions WHERE assessment_id = ? ORDER BY created_at ASC";
        break;
    default:
        echo json_encode(['error' => 'Invalid question type']);
        exit();
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $assessment_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

echo json_encode($questions);
?>
