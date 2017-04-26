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
    
    if($slot == 'mainHand') {
        if ($stmt = $mysqli->prepare("UPDATE party SET main_hand = ?, strength = ?, magic = ?, defense = ?, resistance = ?, haste = ?,
        hp = ?, mp = ?, current_hp = ?, current_mp = ?  WHERE id = ?")) {
            $stmt->bind_param('siiiiiiiiii', $newSlot, $newStr, $newMag, $newDef, $newRes, $newHst, $newHp, $newMp, $currentHp, $currentMp, $partyId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    if($slot == 'offHand') {
        if ($stmt = $mysqli->prepare("UPDATE party SET off_hand = ?, strength = ?, magic = ?, defense = ?, resistance = ?, haste = ?,
        hp = ?, mp = ?, current_hp = ?, current_mp = ?  WHERE id = ?")) {
            $stmt->bind_param('siiiiiiiiii', $newSlot, $newStr, $newMag, $newDef, $newRes, $newHst, $newHp, $newMp, $currentHp, $currentMp, $partyId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    if($slot == 'helm') {
        if ($stmt = $mysqli->prepare("UPDATE party SET helm = ?, strength = ?, magic = ?, defense = ?, resistance = ?, haste = ?,
        hp = ?, mp = ?, current_hp = ?, current_mp = ?  WHERE id = ?")) {
            $stmt->bind_param('siiiiiiiiii', $newSlot, $newStr, $newMag, $newDef, $newRes, $newHst, $newHp, $newMp, $currentHp, $currentMp, $partyId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    if($slot == 'chest') {
        if ($stmt = $mysqli->prepare("UPDATE party SET chest = ?, strength = ?, magic = ?, defense = ?, resistance = ?, haste = ?,
        hp = ?, mp = ?, current_hp = ?, current_mp = ?  WHERE id = ?")) {
            $stmt->bind_param('siiiiiiiiii', $newSlot, $newStr, $newMag, $newDef, $newRes, $newHst, $newHp, $newMp, $currentHp, $currentMp, $partyId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    if($slot == 'accessory') {
        if ($stmt = $mysqli->prepare("UPDATE party SET accessory = ?, strength = ?, magic = ?, defense = ?, resistance = ?, haste = ?,
        hp = ?, mp = ?, current_hp = ?, current_mp = ?  WHERE id = ?")) {
            $stmt->bind_param('siiiiiiiiii', $newSlot, $newStr, $newMag, $newDef, $newRes, $newHst, $newHp, $newMp, $currentHp, $currentMp, $partyId);
            $stmt->execute();
            $stmt->close();
        }
    }

    // add to inventory
    
    echo 'success';
}

$mysqli->close();
?>