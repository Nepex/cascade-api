<?php 
    include '../connection.php';

    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Headers: Content-Type');

    $username = $_GET['username'];
    $email = $_GET['email'];
    $password = $_GET['password'];

    $searchTakenNames = mysqli_query($mysqli, "SELECT * FROM users WHERE username='$username'");
    $nameMatches = mysqli_num_rows($searchTakenNames);
    $searchTakenEmails = mysqli_query($mysqli, "SELECT * FROM users WHERE email='$email'");
    $emailMatches = mysqli_num_rows($searchTakenEmails);

    if ($nameMatches != 0) {
        echo 'username taken';
    } else if ($emailMatches != 0) {
        echo 'email taken';
    } else if (!ctype_alnum($username) || $username == '' || strlen($username) > 15 
    || !filter_var($email, FILTER_VALIDATE_EMAIL) || $email == '' || strlen($email) > 60 || $password == '' || strlen($password) > 20) {
        echo 'validation error';
    } else {
        // create user
        if ($stmt = $mysqli->prepare("INSERT INTO `users` (username, password, email) VALUES (?, ?, ?)")) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param('sss', $username, $hash, $email);
            $stmt->execute();
            
            // create inventory
            if ($stmt = $mysqli->prepare("INSERT INTO `inventory` (username) VALUES (?)")) {
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $stmt->close();
            }

            echo 'success';

            $stmt->close();
        }
    }

    $mysqli->close();
?>