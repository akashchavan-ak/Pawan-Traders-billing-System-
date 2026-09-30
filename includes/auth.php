<?php
/* ---------- LOGOUT ---------- */

if (isset($_GET['logout'])) {
    session_destroy();
    go("index.php");
}

/* ---------- LOGIN GATE ---------- */

if (!isset($_SESSION['user_id'])) {
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = trim($_POST['username'] ?? "");
        $password = $_POST['password'] ?? "";

        $stmt = $conn->prepare("SELECT id,username,password FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            go("index.php");
        }

        $error = "Invalid username or password.";
    }

    require __DIR__ . '/login_view.php';
    exit;
}
