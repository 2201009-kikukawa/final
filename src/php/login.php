<?php
session_start();
require 'db-connect.php';

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

$email = $data['email'];
$pass = $data['password'];

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('SELECT * FROM user WHERE mail = ? AND password = ?');
    $stmt->bindParam(1, $email);
    $stmt->bindParam(2, $pass);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(['message' => 'ログインに失敗しました']);
    } else {
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['user_name'] = $result['user_name'];
        $_SESSION['user_mail'] = $result['mail'];

        echo json_encode(['message' => 'ようこそ']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
