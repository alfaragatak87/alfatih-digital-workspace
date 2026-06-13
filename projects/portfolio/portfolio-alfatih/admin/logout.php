<?php
require_once '../config/constants.php';
require_once '../includes/helpers.php';

// Start session
startSession();

// Destroy session
session_destroy();

// Redirect to login page
setAlert('success', 'You have been logged out successfully');
header('Location: ' . BASE_URL . '/admin/login.php');
exit;