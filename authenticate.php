<?php 
    include './connection.php';
    include './jwt-helper.php';

    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Headers: Content-Type');

    $username = $_GET['username'];
    $password = $_GET['password'];

    if ($stmt = $mysqli->prepare("SELECT username, password FROM users WHERE username=?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($fetchedUser, $fetchedPass);
        $stmt->fetch();
        $stmt->close();
    }

    $hashed = $fetchedPass;
    $unhashed = password_verify($password, $hashed);


    if ($username == '' || $password == '' || strlen($username) > 15 || strlen($password) > 20 || !ctype_alnum($username)) {
        echo 'validation error';
    }
    else if ($unhashed == 0) {
        echo 'incorrect credentials';
    } else {
        if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE username=?")) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($fetchedId);
            $stmt->fetch();
            $stmt->close();
        }

        $token = array();
        $token['id'] = $fetchedId;
        
        echo JWT::encode($token, 'secret_server_key');
    }

    $mysqli->close();
?>