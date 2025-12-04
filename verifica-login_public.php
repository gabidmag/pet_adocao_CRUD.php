<?php

session_start();

function is_logged_in(): bool {
    return isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario']);
}

function require_login(string $login_url = 'login_public.php') {
    if (!is_logged_in()) {
        header("Location: {$login_url}");
        exit;
    }
}
?>