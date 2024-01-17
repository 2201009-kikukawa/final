<?php
require 'db-connect.php';

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

$title = $data['title'];
$url = $data['url'];
$comment = $data['comment'];
$user_id = $data['user_id'];
$tag_id = $data['tag_id'];

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('INSERT INTO bookmark (title, url, comment, user_id, tag_id) VALUES (?, ?, ?, ?, ?)');
    $stmt->bindParam(1, $title);
    $stmt->bindParam(2, $url);
    $stmt->bindParam(3, $comment);
    $stmt->bindParam(4, $user_id);
    $stmt->bindParam(5, $tag_id);

    $stmt->execute();

    $stmtTagCount = $pdo->prepare('UPDATE tag SET tag_count = tag_count + 1 WHERE tag_id = ?');
    $stmtTagCount->bindParam(1, $tag_id);
    $stmtTagCount->execute();

    echo json_encode(['message' => 'ブックマークしました']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'ブックマークに失敗しました']);
}
?>

