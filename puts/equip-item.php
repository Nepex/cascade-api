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
$jobsAllowed = $bodyArray['jobs'];
$itemToEquip = $bodyArray['id'];
$partyId = $bodyArray['partyId'];

$addedRes = $bodyArray['bonusRes'];
$addedHst = $bodyArray['bonusHst'];
$addedDef = $bodyArray['bonusDef'];
$addedStr = $bodyArray['bonusStr'];
$addedMag = $bodyArray['bonusMag'];
$addedMp = $bodyArray['bonusMp'];
$addedHp = $bodyArray['bonusHp'];

$itemToUnEquip = $bodyArray['itemToRemove']['id'];
$removedRes = $bodyArray['itemToRemove']['bonusRes'];
$removedHst = $bodyArray['itemToRemove']['bonusHst'];
$removedDef = $bodyArray['itemToRemove']['bonusDef'];
$removedStr = $bodyArray['itemToRemove']['bonusStr'];
$removedMag = $bodyArray['itemToRemove']['bonusMag'];
$removedMp = $bodyArray['itemToRemove']['bonusMp'];
$removedHp = $bodyArray['itemToRemove']['bonusHp'];

$permitEquip = false;

switch($slot) {
    case 'mainHand':
        $slotSelected = 'main_hand';
        break;
    case 'offHand':
        $slotSelected = 'off_hand';
        break;
    case 'helm':
        $slotSelected = 'helm';
        break;
    case 'chest':
        $slotSelected = 'chest';
        break;
    case 'accessory':
        $slotSelected = 'accessory';
        break;
}


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
            $equippedItem = $row[$slotSelected];
            $job = $row['job'];
        }
    }

    if($jobsAllowed != 'any') {
        for ($i = 0; $i < count($jobsAllowed); $i++) {
            if ($jobsAllowed[$i] == $job) {
                $permitEquip = true;
            }
        }
        
        if (!$permitEquip) {
            echo 'invalid job';
            return;
        }
    }
    
    // if the party member has item equipped, remove it, place it back into inventory
    if ($equippedItem != 'empty') {
        $partyRemoveItemSql = "SELECT * FROM party WHERE id = $partyId";
        $partyRemoveItemResult = $mysqli->query($partyRemoveItemSql);
        if ($partyRemoveItemResult->num_rows) {
            while($row = $partyRemoveItemResult->fetch_assoc()) {
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
        
        $newSlot = 'empty';
        
        if ($stmt = $mysqli->prepare("UPDATE party SET ".$slotSelected." = ?, bonus_strength = ?, bonus_magic = ?, bonus_defense = ?, bonus_resistance = ?, bonus_haste = ?,
        bonus_hp = ?, bonus_mp = ?, current_hp = ?, current_mp = ?  WHERE id = ?")) {
            $stmt->bind_param('siiiiiiiiii', $newSlot, $newStr, $newMag, $newDef, $newRes, $newHst, $newHp, $newMp, $currentHp, $currentMp, $partyId);
            $stmt->execute();
            $stmt->close();
        }
        
        $inventoryAddSql = "SELECT * FROM inventory WHERE username = '$username'";
        $inventoryAddResult = $mysqli->query($inventoryAddSql);
        if ($inventoryAddResult->num_rows) {
            while($row = $inventoryAddResult->fetch_assoc()) {
                $originalAmount = $row[$itemToUnEquip];
                
                if ($originalAmount == 0) {
                    return;
                }
                
                $newAmount = $originalAmount + 1;
            }
            
            if ($stmt = $mysqli->prepare("UPDATE inventory SET ".$itemToUnEquip." = ? WHERE username = ?")) {
                $stmt->bind_param('ss', $newAmount, $username);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    
    // equip new item
    $partyEquipSql = "SELECT * FROM party WHERE id = $partyId";
    $partyEquipResult = $mysqli->query($partyEquipSql);
    if ($partyEquipResult->num_rows) {
        while($row = $partyEquipResult->fetch_assoc()) {
            $newStr = $row['bonus_strength'] + $addedStr;
            $newMag = $row['bonus_magic'] + $addedMag;
            $newDef = $row['bonus_defense'] + $addedDef;
            $newRes = $row['bonus_resistance'] + $addedRes;
            $newHst = $row['bonus_haste'] + $addedHst;
            $newHp = $row['bonus_hp'] + $addedHp;
            $newMp = $row['bonus_mp'] + $addedMp;
        }
    }
    
    if ($stmt = $mysqli->prepare("UPDATE party SET ".$slotSelected." = ?, bonus_strength = ?, bonus_magic = ?, bonus_defense = ?, bonus_resistance = ?, bonus_haste = ?,
    bonus_hp = ?, bonus_mp = ? WHERE id = ?")) {
        $stmt->bind_param('siiiiiiii', $itemToEquip, $newStr, $newMag, $newDef, $newRes, $newHst, $newHp, $newMp, $partyId);
        $stmt->execute();
        $stmt->close();
    }
    
    // remove item from inventory
    $inventorySql = "SELECT * FROM inventory WHERE username = '$username'";
    $inventoryResult = $mysqli->query($inventorySql);
    if ($inventoryResult->num_rows) {
        while($row = $inventoryResult->fetch_assoc()) {
            $originalAmount = $row[$itemToEquip];
            
            if ($originalAmount == 0) {
                return;
            }
            
            $newAmount = $originalAmount - 1;
        }
        
        if ($stmt = $mysqli->prepare("UPDATE inventory SET ".$itemToEquip." = ? WHERE username = ?")) {
            $stmt->bind_param('ss', $newAmount, $username);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    echo 'success';
}

$mysqli->close();
?>