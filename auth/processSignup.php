<?php
session_start();
require_once '../database/db_config.php';


function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        
        $check_email = $conn->prepare("SELECT id FROM accounts WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $result = $check_email->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Email already exists");
        }

      
        $unique_id = generateUUID();
        
      
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

       
        $stmt = $conn->prepare("INSERT INTO accounts (unique_id, first_name, middle_name, last_name, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $unique_id, $first_name, $middle_name, $last_name, $email, $hashed_password);
        
        if (!$stmt->execute()) {
            throw new Exception("Registration failed: " . $stmt->error);
        }

        
        $_SESSION['success_message'] = "Registration successful! You can now log in.";
        
      
        header("Location: ../login.html");
        exit();

    } catch(Exception $e) {
        $_SESSION['signup_errors'] = [$e->getMessage()];
        $_SESSION['form_data'] = [
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'email' => $email
        ];
        header("Location: ../signup.html");
        exit();
    }
} else {
   
    header("Location: ../signup.html");
    exit();
}
?>
