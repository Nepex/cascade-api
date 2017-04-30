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
            $newStr = $row['bonus_strength'] - $removedStr;
            $newMag = $row['bonus_magic'] - $removedMag;
            $newDef = $row['bonus_defense'] - $removedDef;
            $newRes = $row['bonus_resistance'] - $removedRes;
            $newHst = $row['bonus_haste'] - $removedHst;
            $newHp = $row['bonus_hp'] - $removedHp;
            $newMp = $row['bonus_mp'] - $removedMp;

            // check if current hp is higher than hp after unequipping, match it if so
            $hpCheck = $row['hp'] - $newHp;
            $mpCheck = $row['mp'] - $newMp;
            
            if ($row['current_hp'] > $hpCheck) {
                $currentHp = $hpCheck;
            } else {
                $currentHp = $row['current_hp'];
            }
            
            if ($row['current_mp'] > $mpCheck) {
                $currentMp = $mpCheck;
            } else {
                $currentMp = $row['current_mp'];
            }
        }
    }
    
    $emptySlot = 'empty';
    
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

    if ($stmt = $mysqli->prepare("UPDATE party SET ".$unequipSlot." = ?, bonus_strength = ?, bonus_magic = ?, bonus_defense = ?, bonus_resistance = ?, bonus_haste = ?,
    bonus_hp = ?, bonus_mp = ?, current_hp = ?, current_mp = ?  WHERE id = ?")) {
        $stmt->bind_param('siiiiiiiiii', $emptySlot, $newStr, $newMag, $newDef, $newRes, $newHst, $newHp, $newMp, $currentHp, $currentMp, $partyId);
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