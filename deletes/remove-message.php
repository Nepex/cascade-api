<?php
include '../connection.php';
include '../jwt-helper.php';

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: DELETE');

$messageId = $_GET['id'];

$headers = apache_request_headers();
$sentToken = $headers['Authorization'];

if ($sentToken) {
    $decoded = JWT::decode($sentToken, '8725309');
    
    $userId = $decoded->id;
    
    $userSql = "SELECT * FROM users WHERE id = $userId";
    $userResult = $mysqli->query($userSql);
    if ($userResult->num_rows) {
        while($row = $userResult->fetch_assoc()) {
            $username = $row["username"];
        }
    }
    
    // remove message
    if ($messageId == 'all') {
        if ($stmt = $mysqli->prepare("DELETE FROM messages WHERE receiver = ?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        if ($stmt = $mysqli->prepare("DELETE FROM messages WHERE id = ?")) {
            $stmt->bind_param('i', $messageId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    echo 'success';
}

$mysqli->close();
?>