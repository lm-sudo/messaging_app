<?php

session_start();
//Check that a user is logged in.
if (isset($_SESSION['username']))
{
    //A message is submitted to be sent to another user.
    if(isset($_POST['message']) && isset($_POST['to_id']))
    {
        //Include database file.
        include "../db.conn.php";
        //Store message, sender, and recipient variables.
        $message = $_POST['message'];
        $to_id = $_POST['to_id'];
        $from_id = $_SESSION['user_id'];
        //Insert log of chat.
        $sql = "INSERT INTO chats (from_id, to_id, message) VALUES(:from_id, :to_id, :message)";
        $res = $conn->prepare($sql);
        $res = $res->execute([$from_id, $to_id, $message]);
        //Check that insert statement was successful.
        if($res)
        {
            $sql2 = "SELECT * FROM conversations WHERE (user_1 = :from_id AND user_2 = :to_id)
            OR (user_1 = :to_id AND user_2 = :from_id)";
            $res2 = $conn->prepare($sql2);
            $res2->execute([$from_id, $to_id]);
            //Set default time zone.
            define('TIMEZONE', 'US/Eastern');
            date_default_timezone_set(TIMEZONE);
            //Set ~ time of chat.
            $time = date("h:i:s a");
            //It is the first time two users are conversing.
            if($res2->rowCount() == 0)
            {
                //Insert a log of the conversation.
                $sql3 = "INSERT INTO conversations(user_1, user_2)
                VALUES (:user_1, :user_2)";
                $res3 = $conn->prepare($sql3);
                $res3->execute([$from_id, $to_id]);
            }
            ?>
            <p class="rtext align-self-end border rounded p-2 mb-1">
                <?=$message?>
                <small class="d-block"><?=$time?></small>
            </p>
            <?php
        }
    }
}
else
{
    header("Location: ../../index.php");
    exit;
}
?>