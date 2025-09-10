<?php
require_once 'inc/functions.php';
session_unset();
session_destroy();
header('Location: ' . BASE_URL . '/login.php');
exit;
