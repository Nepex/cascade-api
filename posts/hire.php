<?php 
    include '../connection.php';
    include '../jwt-helper.php';

    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    $name = $_GET['name'];
    $job = $_GET['job'];
    $sprite = $_GET['sprite'];
    $jobLevels = 1;
    $experience = 0;
    $experienceNeeded = 500;
    $currentHp = 100;
    $hp = 100;
    $currentMp = 50;
    $mp = 50;

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
                    $partySlotsAvailable = $row['party_slots'] - 1;
                }
            }

            // remove available party slot
            if ($stmt = $mysqli->prepare("UPDATE `users` SET party_slots = ? WHERE id = $userId")) {
                $stmt->bind_param('i', $partySlotsAvailable);
                $stmt->execute();
                
                // create party member
                if ($stmt = $mysqli->prepare("INSERT INTO `party` (owner, name, job, sprite, knight_lvl, mage_lvl, priest_lvl,
                experience, experience_needed, current_hp, hp, current_mp, mp) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $stmt->bind_param('sssssiiiiiiii', $username, $name, $job, $sprite, $jobLevels, $jobLevels, $jobLevels, $experience, $experienceNeeded, $currentHp, $hp, $currentMp, $mp);
                    $stmt->execute();
                    $stmt->close();
                }

                echo 'success';

                $stmt->close();
            } 
        }
    }

    $mysqli->close();
?>