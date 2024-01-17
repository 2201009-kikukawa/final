<?php
require 'db-connect.php';

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

$email = $data['email'];
$user = $data['username'];
$pass = $data['password'];

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('INSERT INTO user (user_name, mail, password) VALUES (?, ?, ?)');
    $stmt->bindParam(1, $user);
    $stmt->bindParam(2, $email);
    $stmt->bindParam(3, $pass);

    $stmt->execute();

    echo json_encode(['message' => 'ユーザーが正常に追加されました']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'ユーザーの追加に失敗しました', 'error' => $e->getMessage()]);
}
?>
