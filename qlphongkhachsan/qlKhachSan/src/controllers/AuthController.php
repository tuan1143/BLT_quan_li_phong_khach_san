<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\Auth;

class AuthController
{
    public function login($request)
    {
        // Logic for handling user login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $request['username'];
            $password = $request['password'];

            // Validate input
            if (empty($username) || empty($password)) {
                return ['error' => 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!'];
            }

            $user = User::findByUsername($username);
            if ($user && password_verify($password, $user['password_hash'])) {
                Auth::login($user);
                header('Location: /dashboard.php');
                exit;
            } else {
                return ['error' => 'Tên đăng nhập hoặc mật khẩu không chính xác!'];
            }
        }
        return [];
    }

    public function register($request)
    {
        // Logic for handling user registration
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $request['username'];
            $password = $request['password'];
            $confirmPassword = $request['confirm_password'];

            // Validate input
            if (empty($username) || empty($password) || empty($confirmPassword)) {
                return ['error' => 'Vui lòng nhập đầy đủ thông tin!'];
            }

            if ($password !== $confirmPassword) {
                return ['error' => 'Mật khẩu không khớp!'];
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $user = new User($username, $hashedPassword);
            if ($user->save()) {
                header('Location: /login.php');
                exit;
            } else {
                return ['error' => 'Đăng ký không thành công!'];
            }
        }
        return [];
    }

    public function logout()
    {
        // Logic for handling user logout
        Auth::logout();
        header('Location: /login.php');
        exit;
    }
}
?>