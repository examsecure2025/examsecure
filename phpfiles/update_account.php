<?php
ob_start();
require_once '../auth/check_session.php';
require_once '../database/db_config.php';
ob_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['action'])) {
    echo json_encode(['success'=>false,'message'=>'Invalid payload']);
    exit();
}

$user_id = $_SESSION['unique_id'];

try {
    if ($input['action'] === 'update_details') {
        $first = trim((string)($input['first_name'] ?? ''));
        $middle = trim((string)($input['middle_name'] ?? ''));
        $last = trim((string)($input['last_name'] ?? ''));
        $email = trim((string)($input['email'] ?? ''));

        if ($first === '' || $last === '' || $email === '') {
            echo json_encode(['success'=>false,'message'=>'First name, last name, and email are required']);
            exit();
        }

        // Optional: validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success'=>false,'message'=>'Invalid email address']);
            exit();
        }

        // Ensure email uniqueness for this owner (except self)
        $sql = "SELECT unique_id FROM accounts WHERE email = ? AND unique_id <> ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $email, $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            echo json_encode(['success'=>false,'message'=>'Email is already in use']);
            exit();
        }

        $sql = "UPDATE accounts SET first_name = ?, middle_name = ?, last_name = ?, email = ? WHERE unique_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $first, $middle, $last, $email, $user_id);
        $stmt->execute();
        echo json_encode(['success'=>true,'message'=>'Details updated successfully']);
        exit();

    } elseif ($input['action'] === 'update_password') {
        $current = (string)($input['current_password'] ?? '');
        $new = (string)($input['new_password'] ?? '');
        $confirm = (string)($input['confirm_password'] ?? '');

        if ($current === '' || $new === '' || $confirm === '') {
            echo json_encode(['success'=>false,'message'=>'All password fields are required']);
            exit();
        }
        if ($new !== $confirm) {
            echo json_encode(['success'=>false,'message'=>'New password and confirmation do not match']);
            exit();
        }
        if (strlen($new) < 8) {
            echo json_encode(['success'=>false,'message'=>'New password must be at least 8 characters']);
            exit();
        }

        // Fetch existing hash
        $sql = "SELECT password FROM accounts WHERE unique_id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!$res || $res->num_rows === 0) {
            echo json_encode(['success'=>false,'message'=>'Account not found']);
            exit();
        }
        $row = $res->fetch_assoc();
        $hash = $row['password'];
        if (!password_verify($current, $hash)) {
            echo json_encode(['success'=>false,'message'=>'Current password is incorrect']);
            exit();
        }

        $newHash = password_hash($new, PASSWORD_BCRYPT);
        $sql = "UPDATE accounts SET password = ? WHERE unique_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $newHash, $user_id);
        $stmt->execute();
        echo json_encode(['success'=>true,'message'=>'Password updated successfully']);
        exit();
    } else {
        echo json_encode(['success'=>false,'message'=>'Unsupported action']);
        exit();
    }
} catch (Exception $e) {
    error_log('update_account error: '.$e->getMessage());
    echo json_encode(['success'=>false,'message'=>'Server error. Please try again.']);
    exit();
}


