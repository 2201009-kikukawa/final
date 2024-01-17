<?php
require 'db-connect.php';

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$tag_id = isset($_GET['tag_id']) ? $_GET['tag_id'] : null;

if (!$user_id) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User ID not provided']);
    exit;
}

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!$tag_id) {
        $stmtcnt = $pdo->prepare('SELECT COUNT(bookmark_id) FROM bookmark WHERE user_id = ?');
        $stmtcnt->bindParam(1, $user_id);
        $stmtcnt->execute();
        $getbookmarkcnt = $stmtcnt->fetchColumn();

        $stmt = $pdo->prepare('SELECT * FROM bookmark WHERE user_id = ?');
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $getbookmarks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmtcnt = $pdo->prepare('SELECT COUNT(bookmark_id) FROM bookmark WHERE user_id = ? AND tag_id = ?');
        $stmtcnt->bindParam(1, $user_id);
        $stmtcnt->bindParam(2, $tag_id);
        $stmtcnt->execute();
        $getbookmarkcnt = $stmtcnt->fetchColumn();

        $stmt = $pdo->prepare('SELECT * FROM bookmark WHERE user_id = ? AND tag_id = ?');
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $tag_id);
        $stmt->execute();
        $getbookmarks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!$getbookmarks) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(['bookmarks' => $getbookmarks, 'bookmarkcnt' => $getbookmarkcnt]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>
