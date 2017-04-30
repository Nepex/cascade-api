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

$oldPass = $bodyArray['oldPassword'];
$newPass = $bodyArray['newPassword'];

if ($sentToken) {
    $decoded = JWT::decode($sentToken, '8725309');
    
    $userId = $decoded->id;
    
    if ($stmt = $mysqli->prepare("SELECT password FROM users WHERE id=?")) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($fetchedPass);
        $stmt->fetch();
        $stmt->close();
    }
    
    $unhashed = password_verify($oldPass, $fetchedPass);
    
    if ($oldPass == '' || $newPass == '' || strlen($oldPass) > 20 || strlen($newPass) > 20) {
        echo 'validation error';
    }
    else if ($unhashed == 0) {
        echo 'incorrect credentials';
    } else {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        
        if ($stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?")) {
            $stmt->bind_param('si', $hash, $userId);
            $stmt->execute();
            $stmt->close();
        }
        
        echo 'success';
    }
}

$mysqli->close();
?>