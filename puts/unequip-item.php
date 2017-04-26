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

$slot = $bodyArray['slot'];
$hp = $bodyArray['hp'];
$id = $bodyArray['id'];
$partyId = $bodyArray['partyId'];
$removedRes = $bodyArray['bonusRes'];
$removedHst = $bodyArray['bonusHst'];
$removedDef = $bodyArray['bonusDef'];
$removedStr = $bodyArray['bonusStr'];
$removedMag = $bodyArray['bonusMag'];
$removedMp = $bodyArray['bonusMp'];
$removedHp = $bodyArray['bonusHp'];

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
            $newStr = $row['strength'] - $removedStr;
            $newMag = $row['magic'] - $removedMag;
            $newDef = $row['defense'] - $removedDef;
            $newRes = $row['resistance'] - $removedRes;
            $newHst = $row['haste'] - $removedHst;
            $newHp = $row['hp'] - $removedHp;
            $newMp = $row['mp'] - $removedMp;
            
            if ($row['current_hp'] > $newHp) {
                $currentHp = $newHp;
            } else {
                $currentHp = $row['current_hp'];
            }
            
            if ($row['current_mp'] > $newMp) {
                $currentMp = $newMp;
            } else {
                $currentMp = $row['current_mp'];
            }
        }
    }
    
    $newSlot = 'empty';
    
    // unequip the item and remove the item's bonuses
    switch($slot) {
        case 'mainHand':
        $unequipSlot = 'main_hand';
        break;
        case 'offHand':
        $unequipSlot = 'off_hand';
        break;
        case 'helm':
        $unequipSlot = 'helm';
        break;
        case 'chest':
        $unequipSlot = 'chest';
        break;
        case 'accessory':
        $unequipSlot = 'accessory';
        break;
    }
    
    if ($stmt = $mysqli->prepare("UPDATE party SET ".$unequipSlot." = ?, strength = ?, magic = ?, defense = ?, resistance = ?, haste = ?,
    hp = ?, mp = ?, current_hp = ?, current_mp = ?  WHERE id = ?")) {
        $stmt->bind_param('siiiiiiiiii', $newSlot, $newStr, $newMag, $newDef, $newRes, $newHst, $newHp, $newMp, $currentHp, $currentMp, $partyId);
        $stmt->execute();
        $stmt->close();
    }

    // add the item back into inventory
    $inventorySql = "SELECT * FROM inventory WHERE username = '$username'";
    $inventoryResult = $mysqli->query($inventorySql);
    if ($inventoryResult->num_rows) {
        while($row = $inventoryResult->fetch_assoc()) {
            $originalAmount = $row[$id];

            $newAmount = $originalAmount + 1;
        }
    }

    if ($stmt = $mysqli->prepare("UPDATE inventory SET ".$id." = ? WHERE username = ?")) {
        $stmt->bind_param('ss', $newAmount, $username);
        $stmt->execute();
        $stmt->close();
    }
     
    echo 'success';
}

$mysqli->close();
?>