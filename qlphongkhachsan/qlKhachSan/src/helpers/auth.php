<?php
// auth.php - Helper functions for user authentication

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function login($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['ho_ten'] = $user['ho_ten'];
    $_SESSION['chuc_vu'] = $user['chuc_vu'];
}

function logout() {
    session_start();
    session_unset();
    session_destroy();
}

function redirectToLogin() {
    header('Location: /login.php');
    exit;
}

function redirectToDashboard() {
    header('Location: /dashboard.php');
    exit;
}
?>