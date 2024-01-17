<?php
require 'db-connect.php';

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User ID not provided']);
    exit;
}

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmtCount = $pdo->prepare('SELECT COUNT(tag_id) as tagscount FROM tag WHERE user_id = ?');
    $stmtCount->bindParam(1, $user_id);
    $stmtCount->execute();
    $tagscount = $stmtCount->fetch(PDO::FETCH_ASSOC);

    $stmtTags = $pdo->prepare('SELECT * FROM tag WHERE user_id = ?');
    $stmtTags->bindParam(1, $user_id);
    $stmtTags->execute();
    $gettags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);

    if (!$gettags) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(['gettags' => $gettags, 'tagscount' => $tagscount['tagscount']]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>

