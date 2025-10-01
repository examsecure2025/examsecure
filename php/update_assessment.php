<?php
require_once '../auth/check_session.php';
require_once '../database/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['assessment_id']) || !isset($input['title']) || !isset($input['year_course']) || 
    !isset($input['sections']) || !isset($input['course_code']) || !isset($input['timer']) || 
    !isset($input['status']) || !isset($input['school_year']) || !isset($input['schedule_date']) || 
    !isset($input['schedule_time']) || !isset($input['close_date']) || !isset($input['close_time'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
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

// Since your database only has year_course column, we don't need to split it
// But we'll keep this for compatibility in case you add separate columns later
$year_course_parts = explode(' ', $input['year_course'], 2);
$year = $year_course_parts[0] ?? '';
$course = $year_course_parts[1] ?? '';

// Convert time format to 24-hour format for database
function convertTimeTo24Hour($time12Hour) {
    $time = DateTime::createFromFormat('h:i A', $time12Hour);
    return $time ? $time->format('H:i:s') : '00:00:00';
}

$schedule_time = convertTimeTo24Hour($input['schedule_time']);
$close_time = convertTimeTo24Hour($input['close_time']);

// Combine date and time for database
$schedule_datetime = $input['schedule_date'] . ' ' . $schedule_time;
$close_datetime = $input['close_date'] . ' ' . $close_time;

try {
    // Update assessment with year_course column (your actual database structure)
    $sql = "UPDATE created_assessments SET 
            title = ?, year_course = ?, sections = ?, course_code = ?, 
            timer = ?, status = ?, school_year = ?, schedule = ?, closing_time = ? 
            WHERE unique_id = ? AND owner_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssss", 
        $input['title'], 
        $input['year_course'],
        $input['sections'], 
        $input['course_code'], 
        $input['timer'], 
        $input['status'], 
        $input['school_year'], 
        $schedule_datetime, 
        $close_datetime, 
        $assessment_id, 
        $owner_id
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Assessment updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update assessment']);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating assessment: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
