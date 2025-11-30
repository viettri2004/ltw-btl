<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Utils/JWT.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'full_name' => trim($_POST['full_name']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'phone' => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? '')
            ];

            // Validation
            if (empty($data['full_name']) || empty($data['email']) || empty($data['password'])) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
                header('Location: register.php');
                exit;
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Email không hợp lệ.';
                header('Location: register.php');
                exit;
            }

            if (strlen($data['password']) < 6) {
                $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
                header('Location: register.php');
                exit;
            }

            if ($this->userModel->findByEmail($data['email'])) {
                $_SESSION['error'] = 'Email đã được sử dụng.';
                header('Location: register.php');
                exit;
            }

            if ($this->userModel->create($data)) {
                $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                header('Location: login.php');
                exit;
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra. Vui lòng thử lại.';
                header('Location: register.php');
                exit;
            }
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Vui lòng điền email và mật khẩu.';
                header('Location: login.php');
                exit;
            }

            $user = $this->userModel->findByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                $payload = [
                    'iss' => 'techstore',
                    'iat' => time(),
                    'exp' => time() + (7 * 24 * 60 * 60), // 7 days
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];

                $token = JWT::encode($payload);
                setcookie('auth_token', $token, time() + (7 * 24 * 60 * 60), '/', '', false, true); // HttpOnly

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];

                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error'] = 'Email hoặc mật khẩu không đúng.';
                header('Location: login.php');
                exit;
            }
        }
    }

    public function logout() {
        setcookie('auth_token', '', time() - 3600, '/');
        session_destroy();
        header('Location: index.php');
        exit;
    }

    public static function getCurrentUser() {
        if (isset($_COOKIE['auth_token'])) {
            $payload = JWT::decode($_COOKIE['auth_token']);
            if ($payload) {
                $userModel = new User();
                return $userModel->findById($payload['user_id']);
            }
        }
        return null;
    }

    public static function requireAuth() {
        $user = self::getCurrentUser();
        if (!$user) {
            header('Location: login.php');
            exit;
        }
        return $user;
    }
}
?>