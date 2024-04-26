<?php

function lastChat($id_1, $id_2, $conn)
{
    $sql = "SELECT * FROM chats WHERE (from_id=:id_1 AND to_id=:id_2)
            OR (to_id=:id_1 AND from_id=:id_2) ORDER BY chat_id DESC LIMIT 1";
    $res = $conn->prepare($sql);
    $res->execute([$id_1, $id_2]);

    if($res->rowCount() > 0)
    {
        $chat = $res->fetch();
        return $chat['message'];
    }
    else
    {
        $chat = '';
        return $chat;
    }
}
?>