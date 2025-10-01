<?php
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
 
    header("Location: login.html?error=" . urlencode("Please log in to access this page."));
    exit();
}


if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 12800)) {

    session_unset();
    session_destroy();
    header("Location: login.html?error=" . urlencode("Your session has expired. Please log in again."));
    exit();
}


$_SESSION['login_time'] = time();