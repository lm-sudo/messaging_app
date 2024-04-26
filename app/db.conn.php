<?php

// server name
$server_name = "172.25.0.2";
// user name 
$user_name = "kh";
// password
$password = "Password";
// postgres port
$port = "5432";

// database name
$db_name = "chat_app_db";

//$conn_string = "host=$server_name port=$port dbname=$db_name user=$user_name password=$password";
//$conn = pg_connect($conn_string);

//Connect to a database named Sakila on localhost at port 5432.

try {
    $conn = new PDO("pgsql:host=$server_name;dbname=$db_name;user=$user_name;password=$password");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
  echo "Connection failed : ". $e->getMessage();
}
?>