<?php
session_start();
require_once '../database/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
     
        $stmt = $conn->prepare("SELECT id, unique_id, first_name, middle_name, last_name, email, password FROM accounts WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            
            if (password_verify($password, $user['password'])) {
              
                session_regenerate_id(true);
                
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['unique_id'] = $user['unique_id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['middle_name'] = $user['middle_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = trim($user['first_name'] . ' ' . ($user['middle_name'] ? $user['middle_name'] . ' ' : '') . $user['last_name']);
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();

       
                header("Location: ../dashboard.php");
                exit();
            } else {
                throw new Exception("Invalid email or password");
            }
        } else {
            throw new Exception("Invalid email or password");
        }
    } catch(Exception $e) {
        
        $error_message = urlencode($e->getMessage());
        header("Location: ../login.html?error=" . $error_message);
        exit();
    }
} else {
   
    header("Location: ../login.html");
    exit();
}
?>
