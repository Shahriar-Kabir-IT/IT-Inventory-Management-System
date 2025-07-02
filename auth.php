<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

function isAdmin() {
    return $_SESSION['user_type'] === 'admin';
}

function getUserFactory() {
    return $_SESSION['factory'];
}
?>