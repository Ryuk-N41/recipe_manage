<?php
session_start();

require_once '../config/db.php';
require_once '../controllers/UserController.php';

$controller = new UserController($conn);
$error = '';
$isJson = false;

/* ---------- Handle POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Detect JSON request
    if (isset($_SERVER['CONTENT_TYPE']) &&
        strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $isJson = true;
        $data = json_decode(file_get_contents("php://input"), true) ?? [];
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
    }

    /* ---------- Validation ---------- */
    if ($username === '' || $password === '') {
        $error = "All fields are required.";

        if ($isJson) {
            echo json_encode(["status" => "error", "message" => $error]);
            exit;
        }
    } else {

        /* ---------- Login ---------- */
        $result = $controller->login($username, $password);

        if ($result === true) {
            if ($isJson) {
                echo json_encode(["status" => "success"]);
                exit;
            } else {
                header("Location: home.php");
                exit;
            }
        } else {
            $error = $result;

            if ($isJson) {
                echo json_encode(["status" => "error", "message" => $error]);
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recipe Management - Login</title>
    <style>
        body{
            font-family:Arial,sans-serif;
            background:#f4f4f4;
            margin:0;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh
        }
        .login-container{
            background:#fff;
            border:1px solid #ddd;
            border-radius:6px;
            padding:20px;
            width:300px;
            text-align:center
        }
        h2{margin-bottom:15px}
        input{
            width:100%;
            padding:8px;
            margin-bottom:10px;
            border:1px solid #ddd;
            border-radius:5px
        }
        button{
            padding:8px;
            width:100%;
            background:#333;
            color:#fff;
            border:none;
            border-radius:5px;
            cursor:pointer
        }
        button:hover{background:#555}
        .error{color:red;margin-bottom:10px}
        a{display:block;margin-top:10px;color:#333;text-decoration:none}
        a:hover{text-decoration:underline}
    </style>
</head>

<body>

<div class="login-container">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form id="loginForm">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <a href="register.php">Need an account? Register</a>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = this;
    const username = form.username.value.trim();
    const password = form.password.value.trim();
    let errorBox = document.querySelector('.error');

    if (!username || !password) {
        if (!errorBox) {
            errorBox = document.createElement('p');
            errorBox.className = 'error';
            form.before(errorBox);
        }
        errorBox.textContent = 'All fields are required.';
        return;
    }

    const res = await fetch('login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    });

    const result = await res.json();

    if (result.status === 'success') {
        window.location.href = 'home.php';
    } else {
        if (!errorBox) {
            errorBox = document.createElement('p');
            errorBox.className = 'error';
            form.before(errorBox);
        }
        errorBox.textContent = result.message;
    }
});
</script>

</body>
</html>
