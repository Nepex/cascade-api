<?php 
    include '../connection.php';
    include '../jwt-helper.php';

    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    $name = $_GET['name'];
    $job = $_GET['job'];
    $sprite = $_GET['sprite'];
    $level = 1;
    $experience = 0;
    $experienceNeeded = 500;
    $statPoints = 0;
    $helm = 'leather_cap';
    $chest = 'leather_vest';

    $bonusStr = 0;
    $bonusMag = 0;
    $bonusDef = 2;
    $bonusRes = 0;
    $bonusHst = 0;
    $bonusHp = 0;
    $bonusMp = 0;

    $mainHand = '';

    // stats depending on job
    if ($job == 'Knight') {
        $currentHp = 125;
        $hp = 125;
        $currentMp = 15;
        $mp = 15;
        $strength = 20;
        $magic = 5;
        $defense = 15;
        $resistance = 15;
        $haste = 10;
        $mainHand =  'practice_sword';
        $bonusStr = 1;
    } else if ($job == 'Mage') {
        $currentHp = 99;
        $hp = 99;
        $currentMp = 63;
        $mp = 63;
        $strength = 5;
        $magic = 25;
        $defense = 10;
        $resistance = 10;        
        $haste = 10;
        $mainHand = 'practice_wand';
        $bonusMag = 1;
    } else if ($job == 'Priest') {
        $currentHp = 115;
        $hp = 115;
        $currentMp = 55;
        $mp = 55;
        $strength = 5;
        $magic = 20;
        $defense = 15;
        $resistance = 15;        
        $haste = 10;
        $mainHand = 'practice_wand';   
        $bonusMag = 1;     
    }

    $headers = apache_request_headers();
    $sentToken = $headers['Authorization'];
    
    if ($sentToken) {
        $decoded = JWT::decode($sentToken, '8725309');

        $userId = $decoded->id;

        $sql = "SELECT * FROM users WHERE id = $userId";
        $result = $mysqli->query($sql);
        if ($result->num_rows) {
            while($row = $result->fetch_assoc()) {
                $username = $row['username'];
            }
        }

        $searchTakenNames = mysqli_query($mysqli, "SELECT * FROM party WHERE owner='$username' AND name='$name'");
        $nameMatches = mysqli_num_rows($searchTakenNames);

        if (!ctype_alnum($name) || $name == '' || strlen($name) > 15 || $job == '' || strlen($job) > 15 ||
        $sprite == '' || strlen($sprite) > 15) {
            echo 'validation error';
        }
        else if ($nameMatches != 0) {
            echo 'name taken';
        } else {
            // create party member
            if ($stmt = $mysqli->prepare("INSERT INTO `party` (owner, name, job, sprite, level,
            experience, experience_needed, strength, magic, defense, resistance, haste, current_hp, hp, current_mp, mp, bonus_strength, 
            bonus_magic, bonus_defense, bonus_resistance, bonus_haste, bonus_hp, bonus_mp, stat_points, helm, chest, main_hand) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $stmt->bind_param('sssssiiiiiiiiiiiiiiiiiiisss', $username, $name, $job, $sprite, $level, $experience, $experienceNeeded, 
                $strength, $magic, $defense, $resistance, $haste, $currentHp, $hp, $currentMp, $mp, $bonusStr, $bonusMag, $bonusDef,
                $bonusRes, $bonusHst, $bonusHp, $bonusMp, $statPoints, $helm, $chest, $mainHand);
                $stmt->execute();
                $stmt->close();
            }

            // insert spells
            if ($job === 'Mage') {
                $spell = 'Fireball';
                $cost = 5;
                $base = 50;
                $spellType = 'Magic';
                $description = 'Deals a small amount of magic damage to an enemy.';
                if ($stmt = $mysqli->prepare("INSERT INTO `spells_learned` (username, party_member, spell_name, cost, base, spell_type, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                    $stmt->bind_param('sssiiss', $username, $name, $spell, $cost, $base, $spellType, $description);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            if ($job === 'Priest') {
                if ($stmt = $mysqli->prepare("INSERT INTO `spells_learned` (username, party_member, spell_name, cost, base, spell_type, description)
                VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                    $spell = 'Cure';
                    $cost = 5;
                    $base = 80;
                    $spellType = 'Heal';
                    $description = 'Heals a party member for a small amount.';
                    $stmt->bind_param('sssiiss', $username, $name, $spell, $cost, $base, $spellType, $description);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            
            echo 'success';
        }
    }

    $mysqli->close();
?>