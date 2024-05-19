<?php

// CREATE USER 'hotel_admin'@'localhost' IDENTIFIED BY 'hotel_admin';
// GRANT ALL PRIVILEGES ON hotel_db_v2.* TO 'hotel_admin'@'localhost';

// $db_socket = '/tmp/mysql.sock'; // Path to MySQL socket file
// $db_name = 'hotel_db_v2';
// $db_user_name = 'hotel_admin';
// $db_user_pass = 'hotel_admin';

$db_name = 'mysql:host=localhost;dbname=hotel_db';
$db_user_name = 'root';
$db_user_pass = '';

try {
   $conn = new PDO($db_name, $db_user_name, $db_user_pass);
   // $conn = new PDO("mysql:unix_socket=$db_socket;dbname=$db_name", $db_user_name, $db_user_pass);
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   echo "Connection failed: " . $e->getMessage();
}

// $db_name = 'mysql:host=localhost;dbname=hotel_db';
// $db_user_name = 'root';
// $db_user_pass = '';

// $conn = new PDO($db_name, $db_user_name, $db_user_pass);

function create_unique_id()
{
   $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
   $rand = array();
   $length = strlen($str) - 1;

   for ($i = 0; $i < 20; $i++) {
      $n = mt_rand(0, $length);
      $rand[] = $str[$n];
   }
   return implode($rand);
}

?>