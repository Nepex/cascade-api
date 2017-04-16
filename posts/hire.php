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
    } else if ($job == 'Mage') {
        $currentHp = 99;
        $hp = 99;
        $currentMp = 63;
        $mp = 63;
        $strength = 5;
        $magic = 25;
        $defense = 10;
        $haste = 10;
    } else if ($job == 'Priest') {
        $currentHp = 115;
        $hp = 115;
        $currentMp = 55;
        $mp = 55;
        $strength = 5;
        $magic = 20;
        $defense = 15;
        $haste = 10;
    }

    $headers = apache_request_headers();
    $sentToken = $headers['Authorization'];
    
    if ($sentToken) {
        $decoded = JWT::decode($sentToken, '8725309');

        $userId = $decoded->id;

        if (!ctype_alnum($name) || $name == '' || strlen($name) > 15 || $job == '' || strlen($job) > 15 ||
        $sprite == '' || strlen($sprite) > 15) {
            echo 'validation error';
        } else {
            $sql = "SELECT * FROM users WHERE id = $userId";
            $result = $mysqli->query($sql);

            if ($result->num_rows) {
                while($row = $result->fetch_assoc()) {
                    $username = $row['username'];
                }
            }

            // create party member
            if ($stmt = $mysqli->prepare("INSERT INTO `party` (owner, name, job, sprite, level,
            experience, experience_needed, strength, magic, defense, haste, current_hp, hp, current_mp, mp, stat_points) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $stmt->bind_param('sssssiiiiiiiiiii', $username, $name, $job, $sprite, $level, $experience, $experienceNeeded, 
                $strength, $magic, $defense, $haste, $currentHp, $hp, $currentMp, $mp, $statPoints);
                $stmt->execute();
                $stmt->close();
            }
            
            echo 'success';
        }
    }

    $mysqli->close();
?>