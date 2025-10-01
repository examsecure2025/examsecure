<?php
require_once '../database/db_config.php';
require_once '../auth/check_session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$owner_id = $_SESSION['unique_id'];

try {
    // Get recent assessments with real-time status counts
    $sql = "
        SELECT 
            ca.unique_id,
            ca.title,
            ca.course_code,
            ca.year_course,
            ca.created_at,
            COUNT(DISTINCT s.session_id) as total_students,
            COUNT(DISTINCT CASE WHEN s.status = 'completed' THEN s.session_id END) as finished_count,
            COUNT(DISTINCT CASE WHEN s.status = 'ongoing' AND (s.tab_switch_count + s.face_left_count + s.face_right_count) <= 3 THEN s.session_id END) as answering_count,
            COUNT(DISTINCT CASE WHEN (s.tab_switch_count + s.face_left_count + s.face_right_count) > 3 THEN s.session_id END) as cheating_count
        FROM created_assessments ca
        LEFT JOIN assessment_sessions s ON ca.unique_id = s.assessment_id
        WHERE ca.owner_id = ?
        GROUP BY ca.unique_id, ca.title, ca.course_code, ca.year_course, ca.created_at
        ORDER BY ca.created_at DESC
        LIMIT 5
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recentAssessments = [];
    while ($row = $result->fetch_assoc()) {
        $recentAssessments[] = $row;
    }
    
    // Get recent cheating alerts (students with >3 warnings in last 24 hours)
    $sql = "
        SELECT 
            s.student_name,
            s.year_section,
            ca.title as assessment_title,
            ca.course_code,
            s.started_at,
            s.completed_at,
            (s.tab_switch_count + s.face_left_count + s.face_right_count) as total_warnings,
            s.session_id,
            ca.unique_id as assessment_id
        FROM assessment_sessions s
        JOIN created_assessments ca ON s.assessment_id = ca.unique_id
        WHERE ca.owner_id = ? 
        AND (s.tab_switch_count + s.face_left_count + s.face_right_count) > 3
        AND s.started_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
        ORDER BY s.started_at DESC
        LIMIT 5
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cheatingAlerts = [];
    while ($row = $result->fetch_assoc()) {
        $cheatingAlerts[] = $row;
    }
    
    // Get recent submissions (completed assessments in last 7 days)
    $sql = "
        SELECT 
            s.student_name,
            s.year_section,
            ca.title as assessment_title,
            ca.course_code,
            s.completed_at,
            COALESCE(SUM(sr.points_earned), 0) as total_score,
            s.session_id,
            ca.unique_id as assessment_id
        FROM assessment_sessions s
        JOIN created_assessments ca ON s.assessment_id = ca.unique_id
        LEFT JOIN student_responses sr ON s.session_id = sr.session_id
        WHERE ca.owner_id = ? 
        AND s.status = 'completed'
        AND s.completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY s.session_id, s.student_name, s.year_section, ca.title, ca.course_code, s.completed_at
        ORDER BY s.completed_at DESC
        LIMIT 5
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recentSubmissions = [];
    while ($row = $result->fetch_assoc()) {
        $recentSubmissions[] = $row;
    }
    
    // Get top scores (highest scoring completed assessments)
    $sql = "
        SELECT 
            s.student_name,
            s.year_section,
            ca.title as assessment_title,
            ca.course_code,
            COALESCE(SUM(sr.points_earned), 0) as total_score,
            COALESCE(SUM(sr.question_points), 0) as total_possible,
            s.completed_at,
            s.session_id,
            ca.unique_id as assessment_id
        FROM assessment_sessions s
        JOIN created_assessments ca ON s.assessment_id = ca.unique_id
        LEFT JOIN student_responses sr ON s.session_id = sr.session_id
        WHERE ca.owner_id = ? 
        AND s.status = 'completed'
        GROUP BY s.session_id, s.student_name, s.year_section, ca.title, ca.course_code, s.completed_at
        HAVING total_possible > 0
        ORDER BY (total_score / total_possible) DESC, total_score DESC
        LIMIT 5
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $topScores = [];
    while ($row = $result->fetch_assoc()) {
        $topScores[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'recentAssessments' => $recentAssessments,
        'cheatingAlerts' => $cheatingAlerts,
        'recentSubmissions' => $recentSubmissions,
        'topScores' => $topScores
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
