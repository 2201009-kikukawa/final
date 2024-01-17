<?php
session_start();
require 'db-connect.php';

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User ID not provided']);
    exit;
}

try {
    $pdo = new PDO($connect,USER,PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('SELECT * FROM user WHERE user_id = ?');
    $stmt->bindParam(1, $user_id);
    $stmt->execute();

    $userProfile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userProfile) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(['userProfile' => $userProfile]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>
