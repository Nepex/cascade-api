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

$memberUsing = $bodyArray['memberUsing'];
$cost = $bodyArray['cost'];

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
    
    // remove mp from caster
    $partySql = "SELECT * FROM party WHERE id = $memberUsing";
    $partyResult = $mysqli->query($partySql);
    if ($partyResult->num_rows) {
        while($row = $partyResult->fetch_assoc()) {
            $currentMp = $row['current_mp'];
            
        }
        
        if ($currentMp < $cost) {
            echo 'not enough mp';
            return;
        }
        
        $newAmount = $currentMp - $cost;
        
        if ($stmt = $mysqli->prepare("UPDATE party SET current_mp = ? WHERE id = ?")) {
            $stmt->bind_param('ii', $newAmount, $memberUsing);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    echo 'success';
}

$mysqli->close();
?>