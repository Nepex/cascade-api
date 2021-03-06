<?php
include '../connection.php';
include '../jwt-helper.php';

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');

$headers = apache_request_headers();
$sentToken = $headers['Authorization'];

$newEmail = file_get_contents('php://input');

$searchTakenEmails = mysqli_query($mysqli, "SELECT * FROM users WHERE email='$newEmail'");
$emailMatches = mysqli_num_rows($searchTakenEmails);

if ($sentToken) {
    $decoded = JWT::decode($sentToken, '8725309');
    
    $userId = $decoded->id;
    
    
    if ($emailMatches != 0) {
        echo 'email taken';
    }
    else {
        if ($stmt = $mysqli->prepare("UPDATE users SET email = ? WHERE id = ?")) {
            $stmt->bind_param('si', $newEmail, $userId);
            $stmt->execute();
            $stmt->close();
        }
        
        echo 'success';
    }
}

$mysqli->close();
?>