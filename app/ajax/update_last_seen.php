<?php

session_start();

if(isset($_SESSION['username']))
{
    include '../db.conn.php';

    $id = $_SESSION['user_id'];

    $sql = "UPDATE users SET last_seen = now() WHERE user_id = :user_id";
    $res = $conn->prepare($sql);
    $res->execute([$id]);
}
else{
    header("Location: ../../index.php");
    exit;
}
?>
