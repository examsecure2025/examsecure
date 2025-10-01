<?php
require_once '../auth/check_session.php';
require_once '../database/db_config.php';
header('Content-Type: application/json');

try {
    $session_id = $_GET['session_id'] ?? '';
    if ($session_id === '') { echo json_encode(['success'=>false,'message'=>'Session ID required']); exit; }

    $owner_id = $_SESSION['unique_id'];
    $sql = "SELECT s.session_id, s.assessment_id, s.student_id, s.student_name, s.year_section, s.started_at, s.completed_at, s.status
            FROM assessment_sessions s JOIN created_assessments ca ON ca.unique_id = s.assessment_id
            WHERE s.session_id = ? AND ca.owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $session_id, $owner_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) { echo json_encode(['success'=>false,'message'=>'Not found or access denied']); exit; }
    $session = $res->fetch_assoc();

    $answers_sql = "SELECT question_id, question_type, question_text, student_answer, correct_answer, question_points, is_correct, submitted_at
                    FROM student_responses WHERE session_id = ? OR (session_id IS NULL AND student_id = ? AND assessment_id = ?) ORDER BY question_id, submitted_at";
    $ans_stmt = $conn->prepare($answers_sql);
    $ans_stmt->bind_param('sss', $session_id, $session['student_id'], $session['assessment_id']);
    $ans_stmt->execute();
    $ans_res = $ans_stmt->get_result();
    $answers = [];
    $earned = 0; $total = 0;
    $byQuestion = [];
    while ($row = $ans_res->fetch_assoc()) {
        $qid = $row['question_id'];
        $byQuestion[$qid] = $row; // overwrite keeps the latest by submitted_at due to ordering
    }
    foreach ($byQuestion as $row) {
        $answers[] = $row;
        $isCorrect = (int)($row['is_correct'] ?? 0) === 1;
        $earned += $isCorrect ? (int)($row['question_points'] ?? 0) : 0;
        $total  += (int)($row['question_points'] ?? 0);
    }

    $spent_seconds = null;
    if (!empty($session['started_at']) && !empty($session['completed_at'])) {
        $spent_seconds = max(0, strtotime($session['completed_at']) - strtotime($session['started_at']));
    }

    echo json_encode(['success'=>true,'session'=>$session,'summary'=>[
        'score_earned'=>$earned,'score_total'=>$total,'answers_count'=>count($answers),'time_spent_seconds'=>$spent_seconds
    ],'answers'=>$answers]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
?>


