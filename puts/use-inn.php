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

$cost = $bodyArray['cost'];

if ($sentToken) {
    $decoded = JWT::decode($sentToken, '8725309');
    
    $userId = $decoded->id;
    
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $mysqli->query($sql);
    if ($result->num_rows) {
        while($row = $result->fetch_assoc()) {
            $username = $row['username'];
            $currency = $row['currency'];
            
            $newCurrency = $currency - $cost;
        }
    }
    
    if ($newCurrency < 0) {
        echo 'insufficient funds';
        return;
    }
    
    $partySql = "SELECT * FROM party WHERE owner = '$username'";
    $partyResult = $mysqli->query($partySql);
    if ($partyResult->num_rows) {
        while($row = $partyResult->fetch_assoc()) {
            $hp = $row['hp'];
            $mp = $row['mp'];
            $partyId = $row['id'];
            
            // restore party members
            if ($stmt = $mysqli->prepare("UPDATE party SET current_hp = ?, current_mp = ? WHERE owner = ? AND id = ?")) {
                $stmt->bind_param('iisi', $hp, $mp, $username, $partyId);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        // remove currency from user
        if ($stmt = $mysqli->prepare("UPDATE users SET currency = ? WHERE id = ?")) {
            $stmt->bind_param('ii', $newCurrency, $userId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    
    echo 'success';
}

$mysqli->close();
?>