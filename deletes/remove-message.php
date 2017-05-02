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
    
    // remove message
    if ($stmt = $mysqli->prepare("DELETE FROM messages WHERE id = ?")) {
        $stmt->bind_param('i', $messageId);
        $stmt->execute();
        $stmt->close();
    }

    
    echo 'success';
}

$mysqli->close();
?>