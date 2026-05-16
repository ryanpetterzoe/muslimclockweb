<?php
require_once __DIR__ . '/../includes/auth.php';
mc_logout();
header('Location: login.php');
