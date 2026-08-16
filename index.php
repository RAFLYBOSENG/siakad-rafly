<?php require_once __DIR__ . '/includes/functions.php'; redirect(logged_in() ? dashboard_path() : 'auth/login.php');
