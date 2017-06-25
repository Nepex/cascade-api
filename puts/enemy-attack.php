<?php
include '../connection.php';
include '../jwt-helper.php';

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');

$headers = apache_request_headers();
$sentToken = $headers['Authorization'];

$entityBody = file_get_contents('php://input');
$bodyArray = json_decode($entityBody, true);

$dmgInflicted = $bodyArray['dmgInflicted'];
$partyId = $bodyArray['id'];

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
    
    $partySql = "SELECT * FROM party WHERE id = $partyId";
    $partyResult = $mysqli->query($partySql);
    if ($partyResult->num_rows) {
        while($row = $partyResult->fetch_assoc()) {
            $currHp = $row['current_hp'];
            
            $newAmount = $currHp - $dmgInflicted;
            
            if ($newAmount < 0) {
                $newAmount = 0;
            }
        }
        
        if ($stmt = $mysqli->prepare("UPDATE party SET current_hp = ? WHERE id = ?")) {
            $stmt->bind_param('ii', $newAmount, $partyId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    echo 'success';
}

$mysqli->close();
?>