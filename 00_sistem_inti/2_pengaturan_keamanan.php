<?php
function h(?string $str): string { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }
function generateCSRF(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function validateCSRF(): bool {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            return false;
        }
    }
    return true;
}
?>