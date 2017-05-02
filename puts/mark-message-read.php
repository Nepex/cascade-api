<?php
include '../connection.php';
include '../jwt-helper.php';

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');

$headers = apache_request_headers();
$sentToken = $headers['Authorization'];

$entityBody = file_get_contents('php://input');
$bodyArray = json_decode($entityBody, true);

$messageId = $_GET['id'];
$seen = 'true';

if ($sentToken) {
    
    $decoded = JWT::decode($sentToken, '8725309');
    
    $userId = $decoded->id;

    if ($stmt = $mysqli->prepare("UPDATE messages SET seen = ? WHERE id = ?")) {
        $stmt->bind_param('si', $seen, $messageId);
        $stmt->execute();
        $stmt->close();
    }
    
    echo 'success';
}

$mysqli->close();
?>