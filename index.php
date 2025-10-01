<?php
session_start();

$isLoggedIn = isset($_SESSION['unique_id']) && !empty($_SESSION['unique_id']);

if ($isLoggedIn) {
    header('Location: assessments.php');
} else {
    header('Location: login.php');
}
exit;
?>


