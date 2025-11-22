<?php
// public/index.php

// Autoload dependencies
require_once '../vendor/autoload.php';

// Start session
session_start();

// Load configuration
require_once '../config/database.php';

// Define routes
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Simple routing
switch ($requestUri) {
    case '/':
        // Load home page or redirect to login
        header('Location: login.php');
        exit;
    case '/login':
        require_once 'login.php';
        break;
    case '/register':
        require_once 'register.php';
        break;
    case '/logout':
        require_once 'logout.php';
        break;
    case '/dashboard':
        require_once 'dashboard.php';
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}

// Nếu đã đăng nhập chuyển tới dashboard, ngược lại tới login
if (isset($_SESSION['user_id']) && $_SESSION['user_id']) {
    header('Location: dashboard.php');
    exit;
}

header('Location: login.php');
exit;