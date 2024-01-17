<?php
require 'db-connect.php';
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

$bookmark_id = $data['bookmark_id'];
$updatecomment = $data['comment'];

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('UPDATE bookmark SET comment = ? WHERE bookmark_id = ?');
    $stmt->bindParam(1, $updatecomment);
    $stmt->bindParam(2, $bookmark_id);
    $stmt->execute();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => '更新に失敗しました']);
}
?>
