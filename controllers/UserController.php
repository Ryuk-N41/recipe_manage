<?php
require_once '../models/User.php';

class UserController {
    private $conn;
    private $user;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->user = new User($conn);
    }

    /* ---------------- Register User ---------------- */
    public function register($username, $email, $password) {
        $username = trim($username);
        $email    = trim($email);

        if ($this->user->usernameExists($username)) {
            return "Username already taken.";
        }

        if ($this->user->emailExists($email)) {
            return "Email already registered.";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';

        if ($this->user->create($username, $email, $hashedPassword, $role)) {
            return true;
        }

        return "Registration failed.";
    }

    /* ---------------- Login User ---------------- */
    public function login($username, $password) {
        $username = trim($username);

        $user = $this->user->getUserByUsername($username);

        if ($user && password_verify($password, $user['password'])) {

            // Session values
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id']  = (int)$user['id'];
            $_SESSION['role']     = $user['role'];

            return true;
        }

        return "Invalid username or password.";
    }

    /* ---------------- Get User ID ---------------- */
    public function getUserId($username) {
        $user = $this->user->getUserByUsername($username);
        return $user ? (int)$user['id'] : null;
    }
}
