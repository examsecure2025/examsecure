<?php
require_once '../database/db_config.php';

header('Content-Type: application/json');

$assessment_id = '40184cd3-1d96-4479-909b-a3e0e6d6f22f';

if (!$assessment_id) {
    echo json_encode(['error' => 'No assessment_id provided']);
    exit;
}

$stmt = $conn->prepare("SELECT owner_id FROM created_assessments WHERE unique_id = ?");
$stmt->bind_param("s", $assessment_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    echo json_encode(['owner_id' => $row['owner_id']]);
} else {
    echo json_encode(['error' => 'Assessment not found']);
}
?>