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
    $helm = 'Leather Cap';
    $chest = 'Leather Vest';
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
        $haste = 10;
        $mainHand =  'Practice Sword';
    } else if ($job == 'Mage') {
        $currentHp = 99;
        $hp = 99;
        $currentMp = 63;
        $mp = 63;
        $strength = 5;
        $magic = 25;
        $defense = 10;
        $haste = 10;
        $mainHand = 'Practice Wand';
    } else if ($job == 'Priest') {
        $currentHp = 115;
        $hp = 115;
        $currentMp = 55;
        $mp = 55;
        $strength = 5;
        $magic = 20;
        $defense = 15;
        $haste = 10;
        $mainHand = 'Practice Wand';        
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
            experience, experience_needed, strength, magic, defense, haste, current_hp, hp, current_mp, mp, stat_points, helm, 
            chest, main_hand) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $stmt->bind_param('sssssiiiiiiiiiiisss', $username, $name, $job, $sprite, $level, $experience, $experienceNeeded, 
                $strength, $magic, $defense, $haste, $currentHp, $hp, $currentMp, $mp, $statPoints, $helm, $chest, $mainHand);
                $stmt->execute();
                $stmt->close();
            }

            // insert spells
            if($job === 'Mage') {
                $spell = 'Fireball';
                $cost = 5;
                $description = 'Deals a small amount of magic damage to an enemy.';
                if ($stmt = $mysqli->prepare("INSERT INTO `spells_learned` (username, party_member, spell_name, cost, description) 
                VALUES (?, ?, ?, ?, ?)")) {
                    $stmt->bind_param('sssis', $username, $name, $spell, $cost, $description);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            if($job === 'Priest') {
                if ($stmt = $mysqli->prepare("INSERT INTO `spells_learned` (username, party_member, spell_name, cost, description) 
                VALUES (?, ?, ?, ?, ?)")) {
                    $spell = 'Cure';
                    $cost = 5;
                    $description = 'Heals a party member for a small amount.';
                    $stmt->bind_param('sssis', $username, $name, $spell, $cost, $description);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            
            echo 'success';
        }
    }

    $mysqli->close();
?>