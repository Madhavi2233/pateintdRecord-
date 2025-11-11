<?php
// File: index.php
// Simple landing route - redirect depending on auth
require_once 'config.php';
if (is_logged_in()) {
    header('Location: patients.php');
} else {
    header('Location: login.php');
}
exit;
