<?php

session_start();

//Check that a user is logged in.
if (isset($_SESSION['username']))
{
    //Check that another user has sent a post request.
    if(isset($_POST['id_2']))
    {
        //Add database file.
        include '../db.conn.php';
        //Define variables for recipient user and sending user.
        $id_1 = $_SESSION['user_id'];
        $id_2 = $_POST['id_2'];
        $opened = 'f';

        $sql = "SELECT * FROM chats WHERE to_id = :to_id AND from_id = :from_id
                ORDER BY chat_id ASC";
        $res = $conn->prepare($sql);
        $res->execute([$id_1, $id_2]);

        if ($res->rowCount() > 0)
        {
            $chats = $res->fetchAll();

            foreach($chats as $chat)
            {
                if ($chat['opened'] == 'f')
                {
                    $opened = 't';
                    $chat_id = $chat['chat_id'];
                    $sql2 = "UPDATE chats SET opened = :opened WHERE chat_id = :chat_id";
                    $res = $conn->prepare($sql2);
                    $res2->execute([$opened, $chat_id]);
                    ?>
                    <p class="ltext border rounded p-2 mb-1">
                        <?=$chat['message']?>
                        <small class="d-block"><?=$chat['created_at']?></small>
                    </p>
                    <?php
                }
            }
        }
    }
}
else
{
    header("Location: ../../index.php");
    exit;
}
?>