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
    // Fetch assessment settings including shuffle options
    $sql = "SELECT shuffle_mcq, shuffle_identification, shuffle_true_false, ai_check_identification 
            FROM created_assessments 
            WHERE unique_id = ? AND owner_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $assessment_id, $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Assessment not found']);
        exit();
    }
    
    $settings = $result->fetch_assoc();
    
    // Convert string values to integers for consistency
    $settings['shuffle_mcq'] = (int)$settings['shuffle_mcq'];
    $settings['shuffle_identification'] = (int)$settings['shuffle_identification'];
    $settings['shuffle_true_false'] = (int)$settings['shuffle_true_false'];
    $settings['ai_check_identification'] = (int)$settings['ai_check_identification'];
    
    echo json_encode(['success' => true, 'settings' => $settings]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching assessment settings: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
