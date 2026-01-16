<?php
session_start();

require_once '../config/db.php';
require_once '../controllers/UserController.php';

$userCtrl = new UserController($conn);
$message = '';
$isApiRequest = false;

/**
 * Detect POST request
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Identify JSON request
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isApiRequest = stripos($contentType, 'application/json') !== false;

    if ($isApiRequest) {
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $uname   = trim($payload['username'] ?? '');
        $mail    = trim($payload['email'] ?? '');
        $pass    = trim($payload['password'] ?? '');
        $cpass   = trim($payload['confirm_password'] ?? '');
    } else {
        $uname = trim($_POST['username'] ?? '');
        $mail  = trim($_POST['email'] ?? '');
        $pass  = trim($_POST['password'] ?? '');
        $cpass = trim($_POST['confirm_password'] ?? '');
    }

    // ---------- Validation ----------
    if (!$uname || !$mail || !$pass || !$cpass) {
        $message = "All fields must be filled.";
    } elseif ($pass !== $cpass) {
        $message = "Password confirmation failed.";
    } elseif (strlen($pass) < 6) {
        $message = "Password length must be 6 or more characters.";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {

        $registerStatus = $userCtrl->register($uname, $mail, $pass);

        if ($registerStatus === true) {
            $_SESSION['username'] = $uname;
            $_SESSION['user_id']  = $userCtrl->getUserId($uname);

            if ($isApiRequest) {
                echo json_encode(['status' => 'success']);
                exit;
            }

            header('Location: login.php');
            exit;
        } else {
            $message = $registerStatus;
        }
    }

    if ($isApiRequest) {
        echo json_encode([
            'status'  => 'error',
            'message' => $message
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recipe Management | Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .box {
            background: #fff;
            padding: 20px;
            width: 300px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .box h2 {
            margin-bottom: 15px;
        }
        .box input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .box button {
            width: 100%;
            padding: 8px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .box button:hover {
            background: #555;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            color: #333;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Create Account</h2>

    <?php if ($message): ?>
        <div class="error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form id="signupForm">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>

    <p id="clientError" class="error"></p>

    <a href="login.php">Already registered? Login</a>
</div>

<script>
document.getElementById('signupForm').addEventListener('submit', async (event) => {
    event.preventDefault();

    const form = event.target;
    const errorBox = document.getElementById('clientError');
    errorBox.textContent = '';

    const data = {
        username: form.username.value.trim(),
        email: form.email.value.trim(),
        password: form.password.value.trim(),
        confirm_password: form.confirm_password.value.trim()
    };

    if (Object.values(data).some(v => v === '')) {
        errorBox.textContent = "All fields are required.";
        return;
    }

    if (data.password !== data.confirm_password) {
        errorBox.textContent = "Passwords do not match.";
        return;
    }

    if (data.password.length < 6) {
        errorBox.textContent = "Password too short.";
        return;
    }

    const res = await fetch('register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });

    const json = await res.json();

    if (json.status === 'success') {
        location.href = 'login.php';
    } else {
        errorBox.textContent = json.message;
    }
});
</script>

</body>
</html>
