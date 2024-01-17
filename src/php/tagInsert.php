<?php
require 'db-connect.php';

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

$tag_name = $data['tagname'];
$user_id = $data['userId'];

$pdo = new PDO($connect, USER, PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare('
    SELECT tag.tag_name, COUNT(bookmark.tag_id) AS tag_count, user.user_id
    FROM tag
    LEFT JOIN bookmark ON tag.tag_id = bookmark.tag_id
    LEFT JOIN user ON user.user_id = bookmark.user_id
    WHERE tag.tag_name = ?
    AND user.user_id = ?
    GROUP BY tag.tag_name, user.user_id
');

$stmt->bindParam(1, $tag_name);
$stmt->bindParam(2, $user_id);

$stmt->execute();
$count = $stmt->rowCount();

if ($count > 0) {
    echo json_encode(['message' => '既に定義されているタグです']);
} else {
    $stmt = $pdo->prepare('INSERT INTO tag (tag_name, tag_count, user_id) VALUES (?, 0, ?)');
    $stmt->bindParam(1, $tag_name);
    $stmt->bindParam(2, $user_id);

    $stmt->execute();

    echo json_encode(['message' => 'タグが正常に追加されました']);
}
