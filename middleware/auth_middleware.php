<?php
session_start();

function protect_page() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /auth/login.php");
        exit();
    }
}

function admin_only() {
    protect_page();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: /auth/index.php");
        exit();
    }
}
