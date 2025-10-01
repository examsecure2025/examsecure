<?php
require_once '../database/db_config.php';
require_once 'check_session.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$db_debug = [
    'created_assessments_structure' => [],
    'accounts_structure' => []
];


$result = $conn->query("DESCRIBE created_assessments");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $db_debug['created_assessments_structure'][] = $row;
    }
}


$result = $conn->query("DESCRIBE accounts");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $db_debug['accounts_structure'][] = $row;
    }
}


$session_debug = [
    'session_id' => session_id(),
    'session_data' => $_SESSION,
    'unique_id' => isset($_SESSION['unique_id']) ? $_SESSION['unique_id'] : null
];


if (isset($_SESSION['unique_id'])) {
    $check_account = $conn->prepare("SELECT unique_id, email FROM accounts WHERE unique_id = ?");
    $check_account->bind_param("s", $_SESSION['unique_id']);
    $check_account->execute();
    $account_result = $check_account->get_result();
    $account_data = $account_result->fetch_assoc();
    
    $session_debug['account_exists'] = !empty($account_data);
    $session_debug['account_data'] = $account_data;
}


if (isset($_GET['debug'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'session' => $session_debug,
        'database' => $db_debug
    ]);
    exit;
}

function generateUniqueId() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function generateAccessCode() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $access_code = '';
    for ($i = 0; $i < 6; $i++) {
        $access_code .= $characters[mt_rand(0, strlen($characters) - 1)];
    }
    return $access_code;
}

function isAccessCodeUnique($conn, $access_code) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM created_assessments WHERE access_code = ?");
    $stmt->bind_param("s", $access_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] === 0;
}

function generateUniqueAccessCode($conn) {
    $max_attempts = 10; // Prevent infinite loops
    $attempts = 0;
    
    do {
        $access_code = generateAccessCode();
        $attempts++;
        
       
        if ($attempts > $max_attempts) {
            throw new Exception("Failed to generate unique access code after $max_attempts attempts");
        }
    } while (!isAccessCodeUnique($conn, $access_code));
    
    return $access_code;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
       
        if (!isset($_SESSION['unique_id'])) {
            throw new Exception("You must be logged in to create an assessment.");
        }
        
        $owner_id = $_SESSION['unique_id'];
        
       
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $owner_id)) {
            throw new Exception("Invalid owner_id format. Expected UUID format.");
        }
        
       
        $check_account = $conn->prepare("SELECT unique_id, email FROM accounts WHERE unique_id = ?");
        $check_account->bind_param("s", $owner_id);
        $check_account->execute();
        $account_result = $check_account->get_result();
        $account_data = $account_result->fetch_assoc();
        
        if (!$account_data) {
            throw new Exception("Account not found. Please log out and log back in.");
        }

       
        $verify_account = $conn->prepare("SELECT COUNT(*) as count FROM accounts WHERE unique_id = ?");
        $verify_account->bind_param("s", $owner_id);
        $verify_account->execute();
        $verify_result = $verify_account->get_result();
        $verify_data = $verify_result->fetch_assoc();

        if ($verify_data['count'] !== 1) {
            throw new Exception("Account verification failed. Please log out and log back in.");
        }

       
        $debug_data = [
            'owner_id' => $owner_id,
            'account_exists' => true,
            'account_data' => $account_data,
            'account_verification' => $verify_data,
            'post_data' => $_POST
        ];

       
        $conn->begin_transaction();

        
        $unique_id = generateUniqueId();
        $access_code = generateUniqueAccessCode($conn);
        
        
        $title = trim($_POST['title']);
        $year_course = trim($_POST['year_course']);
        $sections = trim($_POST['sections']);
        $course_code = trim($_POST['course_code']);
        $timer = $_POST['timer'] === 'No time limit' ? 0 : intval($_POST['timer']);
        $status = strtolower(trim($_POST['status']));
        
        if ($status === 'close') {
            $status = 'closed';
        } elseif ($status === 'active') {
            $status = 'active';
        } else {
            $status = 'active'; 
        }
        $school_year = trim($_POST['school_year']);
        
        // Convert time format from "08:00 AM" to "08:00:00" for MySQL
        function convertTimeFormat($time) {
            return date('H:i:s', strtotime($time));
        }
        
        $schedule = $_POST['schedule_date'] . ' ' . convertTimeFormat($_POST['schedule_time']);
        $closing_time = $_POST['close_date'] . ' ' . convertTimeFormat($_POST['close_time']);
        
        
        $shuffle_mcq = isset($_POST['shuffle_mcq']) && $_POST['shuffle_mcq'] === 'true' ? 1 : 0;
        $shuffle_identification = isset($_POST['shuffle_identification']) && $_POST['shuffle_identification'] === 'true' ? 1 : 0;
        $shuffle_true_false = isset($_POST['shuffle_true_false']) && $_POST['shuffle_true_false'] === 'true' ? 1 : 0;
        $ai_check_identification = isset($_POST['ai_check_identification']) && $_POST['ai_check_identification'] === 'true' ? 1 : 0;

        
        $debug_data['sql_data'] = [
            'unique_id' => $unique_id,
            'access_code' => $access_code,
            'title' => $title,
            'year_course' => $year_course,
            'sections' => $sections,
            'course_code' => $course_code,
            'timer' => $timer,
            'status' => $status,
            'school_year' => $school_year,
            'schedule' => $schedule,
            'closing_time' => $closing_time,
            'owner_id' => $owner_id,
            'shuffle_mcq' => $shuffle_mcq,
            'shuffle_identification' => $shuffle_identification,
            'shuffle_true_false' => $shuffle_true_false,
            'ai_check_identification' => $ai_check_identification
        ];

        // Insert assessment with explicit type casting
        $stmt = $conn->prepare("
            INSERT INTO created_assessments (
                unique_id, access_code, title, year_course, sections, 
                course_code, timer, status, school_year, schedule, 
                closing_time, shuffle_mcq, shuffle_identification, 
                shuffle_true_false, ai_check_identification, owner_id
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");



        $stmt->bind_param("ssssssissssiiiss",
            $unique_id, $access_code, $title, $year_course,
            $sections, $course_code, $timer, $status, $school_year,
            $schedule, $closing_time, $shuffle_mcq, $shuffle_identification,
            $shuffle_true_false, $ai_check_identification, $owner_id
        );

        if (!$stmt->execute()) {
            $error_details = [
                'error' => $stmt->error,
                'errno' => $stmt->errno,
                'sqlstate' => $stmt->sqlstate,
                'debug_data' => $debug_data,
                'bound_params' => [
                    'unique_id' => $unique_id,
                    'access_code' => $access_code,
                    'title' => $title,
                    'year_course' => $year_course,
                    'sections' => $sections,
                    'course_code' => $course_code,
                    'timer' => $timer,
                    'status' => $status,
                    'school_year' => $school_year,
                    'schedule' => $schedule,
                    'closing_time' => $closing_time,
                    'shuffle_mcq' => $shuffle_mcq,
                    'shuffle_identification' => $shuffle_identification,
                    'shuffle_true_false' => $shuffle_true_false,
                    'ai_check_identification' => $ai_check_identification,
                    'owner_id' => $owner_id
                ]
            ];
            throw new Exception("Error creating assessment: " . json_encode($error_details));
        }

        
        if (isset($_POST['questions'])) {
            foreach ($_POST['questions'] as $question) {
                $question_id = generateUniqueId();
                $question_type = $question['type'];
                $question_text = $question['text'];
                $points = $question['points'];

                switch ($question_type) {
                    case 'multiple_choice':
                        $stmt = $conn->prepare("
                            INSERT INTO multiple_choice_questions (
                                question_id, assessment_id, question_text,
                                option_a, option_b, option_c, option_d, option_e,
                                correct_answer, points
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->bind_param("sssssssssi",
                            $question_id, $unique_id, $question_text,
                            $question['options']['a'], $question['options']['b'],
                            $question['options']['c'], $question['options']['d'],
                            $question['options']['e'], $question['correct_answer'], $points
                        );
                        break;

                    case 'identification':
                        $stmt = $conn->prepare("
                            INSERT INTO identification_questions (
                                question_id, assessment_id, question_text,
                                correct_answer, points
                            ) VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt->bind_param("ssssi",
                            $question_id, $unique_id, $question_text,
                            $question['correct_answer'], $points
                        );
                        break;

                    case 'true_false':
                        $stmt = $conn->prepare("
                            INSERT INTO true_false_questions (
                                question_id, assessment_id, question_text,
                                correct_answer, points
                            ) VALUES (?, ?, ?, ?, ?)
                        ");
                        
                        $correct_answer = strtolower($question['correct_answer']) === 'true' ? 1 : 0;
                        $stmt->bind_param("sssii",
                            $question_id, $unique_id, $question_text,
                            $correct_answer, $points
                        );
                        break;
                }

                if (!$stmt->execute()) {
                    throw new Exception("Error adding question: " . $stmt->error);
                }
            }
        }

        
        $conn->commit();

        
        echo json_encode([
            'status' => 'success',
            'message' => 'Assessment created successfully',
            'data' => [
                'unique_id' => $unique_id,
                'access_code' => $access_code
            ]
        ]);

    } catch (Exception $e) {
        
        if (isset($conn)) {
            $conn->rollback();
        }
        
        
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'debug' => isset($debug_data) ? $debug_data : $session_debug
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}
?> 