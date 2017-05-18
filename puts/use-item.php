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

$partyId = $bodyArray['partyId'];
$itemId = $bodyArray['id'];
$healingAmount = $bodyArray['healingAmount'];
$mpHealingAmount = $bodyArray['mpHealingAmount'];
$effect = $bodyArray['effect'];

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
    
    // use item on party member
    $partySql = "SELECT * FROM party WHERE id = $partyId";
    $partyResult = $mysqli->query($partySql);
    if ($partyResult->num_rows) {
        while($row = $partyResult->fetch_assoc()) {
            $hp = $row['hp'];
            $currentHp = $row['current_hp'];
            $mp = $row['mp'];
            $currentMp = $row['current_mp'];
            
            $newHp = $currentHp + $healingAmount;
            $newMp = $currentMp + $mpHealingAmount;
            
            if ($newHp > $hp) {
                $newHp = $hp;
            }
            
            if ($newMp > $mp) {
                $newMp = $mp;
            }
            
            if ($stmt = $mysqli->prepare("UPDATE party SET current_hp = ?, current_mp = ? WHERE id = ?")) {
                $stmt->bind_param('iii', $newHp, $newMp,  $partyId);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    
    // remove item from inventory
    $inventorySql = "SELECT * FROM inventory WHERE username = '$username'";
    $inventoryResult = $mysqli->query($inventorySql);
    if ($inventoryResult->num_rows) {
        while($row = $inventoryResult->fetch_assoc()) {
            $originalAmount = $row[$itemId];
            
            if ($originalAmount == 0) {
                return;
            }
            
            $newAmount = $originalAmount - 1;
        }
        
        if ($stmt = $mysqli->prepare("UPDATE inventory SET ".$itemId." = ? WHERE username = ?")) {
            $stmt->bind_param('ss', $newAmount, $username);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    echo 'success';
}

$mysqli->close();
?>