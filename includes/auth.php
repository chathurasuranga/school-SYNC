<?php

/**
 * Attempt to login a user
 * Returns user array on success, false on failure
 */
function attempt_login($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([trim($username)]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

/**
 * Log in the user by setting session variables
 */
function login_user($user) {
    session_regenerate_id(true); // Security: prevent session fixation
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['role']      = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['last_login'] = time();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Require login or redirect
 */
function require_login() {
    if (!is_logged_in()) {
        redirect_to('login.php');
    }
}

/**
 * Require Admin role
 */
function require_admin() {
    require_login();
    if ($_SESSION['role'] !== 'admin') {
        redirect_to('index.php');
    }
}
?>
