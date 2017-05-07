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

$itemId = $bodyArray['id'];
$buyValue = $bodyArray['buyValue'];

if ($sentToken) {
    $decoded = JWT::decode($sentToken, '8725309');
    
    $userId = $decoded->id;
    
    // give currency to user
    $userSql = "SELECT * FROM users WHERE id = $userId";
    $userResult = $mysqli->query($userSql);
    if ($userResult->num_rows) {
        while($row = $userResult->fetch_assoc()) {
            $username = $row['username'];
            $currency = $row['currency'];

            $newCurrency = $currency - $buyValue;
        }
    }

    if ($newCurrency < 0) {
        echo 'insufficient funds';
        return;
    }
    
    if ($stmt = $mysqli->prepare("UPDATE users SET currency = ? WHERE username = ?")) {
        $stmt->bind_param('is', $newCurrency, $username);
        $stmt->execute();
        $stmt->close();
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
            
            $newAmount = $originalAmount + 1;
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