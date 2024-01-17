<?php
session_start();
require 'db-connect.php';

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

$bookmark_id = $data['bookmark_id'];

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('DELETE FROM bookmark WHERE bookmark_id = ?');
    $stmt->bindParam(1, $bookmark_id);
    $stmt->execute();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'データの削除に失敗しました']);
}
?>