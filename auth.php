<?php
// auth.php — Login / Signup / Logout handler
session_start();
header('Content-Type: application/json');
error_reporting(0); ini_set('display_errors',0);

$db_host="localhost"; $db_user="root"; $db_pass=""; $db_name="koppee_db";
$conn = new mysqli($db_host,$db_user,$db_pass,$db_name);
if($conn->connect_error){ echo json_encode(["success"=>false,"message"=>"DB error."]); exit; }
$conn->set_charset("utf8mb4");

// Auto-create users table
$conn->query("CREATE TABLE IF NOT EXISTS `users`(
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(200) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `avatar` VARCHAR(10) NOT NULL DEFAULT '☕',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY(`user_id`), UNIQUE KEY `uk_email`(`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Add user_id to orders if missing
$conn->query("ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `user_id` INT DEFAULT NULL AFTER `order_id`");

$action = $_POST['action'] ?? '';

// ── SIGNUP ───────────────────────────────────────────────────
if($action === 'signup'){
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password']   ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if(strlen($name)<2)                           { echo json_encode(["success"=>false,"message"=>"Enter your full name."]); exit; }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) { echo json_encode(["success"=>false,"message"=>"Enter a valid email."]); exit; }
    if(strlen($pass)<6)                           { echo json_encode(["success"=>false,"message"=>"Password must be at least 6 characters."]); exit; }

    // Check duplicate
    $chk = $conn->prepare("SELECT user_id FROM users WHERE email=?");
    $chk->bind_param("s",$email); $chk->execute();
    $chk->store_result();
    if($chk->num_rows>0){ echo json_encode(["success"=>false,"message"=>"An account with this email already exists."]); exit; }
    $chk->close();

    $hash   = password_hash($pass, PASSWORD_BCRYPT);
    $avatars = ['☕','🍵','🧋','🍕','🍔','🥤','🍰','🥪'];
    $avatar  = $avatars[array_rand($avatars)];

    $stmt = $conn->prepare("INSERT INTO users(name,email,password,phone,avatar) VALUES(?,?,?,?,?)");
    $stmt->bind_param("sssss",$name,$email,$hash,$phone,$avatar);
    if($stmt->execute()){
        $uid = $conn->insert_id;
        $_SESSION['user'] = ['id'=>$uid,'name'=>$name,'email'=>$email,'avatar'=>$avatar];
        echo json_encode(["success"=>true,"message"=>"Account created!","user"=>["name"=>$name,"avatar"=>$avatar]]);
    } else {
        echo json_encode(["success"=>false,"message"=>"Signup failed. Try again."]);
    }
    $stmt->close();
}

// ── LOGIN ────────────────────────────────────────────────────
elseif($action === 'login'){
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password']   ?? '';

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){ echo json_encode(["success"=>false,"message"=>"Enter a valid email."]); exit; }
    if(empty($pass))                             { echo json_encode(["success"=>false,"message"=>"Enter your password."]); exit; }

    $stmt = $conn->prepare("SELECT user_id,name,email,password,avatar FROM users WHERE email=?");
    $stmt->bind_param("s",$email); $stmt->execute();
    $res = $stmt->get_result(); $row = $res->fetch_assoc(); $stmt->close();

    if(!$row || !password_verify($pass,$row['password'])){
        echo json_encode(["success"=>false,"message"=>"Incorrect email or password."]);
    } else {
        $_SESSION['user'] = ['id'=>$row['user_id'],'name'=>$row['name'],'email'=>$row['email'],'avatar'=>$row['avatar']];
        $conn->query("UPDATE users SET last_login=NOW() WHERE user_id=".$row['user_id']);
        echo json_encode(["success"=>true,"message"=>"Welcome back, ".$row['name']."!","user"=>["name"=>$row['name'],"avatar"=>$row['avatar']]]);
    }
}

// ── LOGOUT ───────────────────────────────────────────────────
elseif($action === 'logout'){
    session_destroy();
    echo json_encode(["success"=>true,"message"=>"Logged out."]);
}

// ── CHECK SESSION ────────────────────────────────────────────
elseif($action === 'check'){
    if(isset($_SESSION['user'])){
        echo json_encode(["success"=>true,"logged_in"=>true,"user"=>$_SESSION['user']]);
    } else {
        echo json_encode(["success"=>true,"logged_in"=>false]);
    }
}

$conn->close();
