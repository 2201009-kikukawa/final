<?php
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$user_mail = isset($_SESSION['user_mail']) ? $_SESSION['user_mail'] : '';

$userProfile = [
    'user_id' => $user_id,
    'user_name' => $user_name,
    'user_mail' => $user_mail,
];

header('Content-Type: application/json');
echo json_encode(['userProfile' => $userProfile]);
?>
