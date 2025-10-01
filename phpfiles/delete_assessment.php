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
    echo json_encode(['success' => false, 'message' => 'Missing assessment ID']);
    exit();
}

$assessment_id = $input['assessment_id'];
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
    // Delete all questions first (due to foreign key constraints)
    // Delete MCQ questions
    $sql = "DELETE FROM multiple_choice_questions WHERE assessment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $assessment_id);
    $stmt->execute();
    
    // Delete identification questions
    $sql = "DELETE FROM identification_questions WHERE assessment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $assessment_id);
    $stmt->execute();
    
    // Delete true/false questions
    $sql = "DELETE FROM true_false_questions WHERE assessment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $assessment_id);
    $stmt->execute();
    
    // Delete student responses (this includes assessment results/submissions)
    $sql = "DELETE FROM student_responses WHERE assessment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $assessment_id);
    $stmt->execute();
    
    // Finally, delete the assessment
    $sql = "DELETE FROM created_assessments WHERE unique_id = ? AND owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $assessment_id, $owner_id);
    
    if ($stmt->execute()) {
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Assessment deleted successfully']);
    } else {
        throw new Exception('Failed to delete assessment');
    }
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error deleting assessment: ' . $e->getMessage()]);
}
?>
