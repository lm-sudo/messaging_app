<?php

function getChats($id_1, $id_2, $conn)
{
    $sql = "SELECT * FROM chats WHERE (from_id = :chat_1 AND to_id = :chat_2) 
            OR (to_id = :chat_1 AND from_id = :chat_2)
            ORDER BY chat_id ASC";
    $res = $conn->prepare($sql);
    $res->execute([$id_1, $id_2]);

    if($res->rowCount() > 0)
    {
        $chats = $res->fetchAll();
        return $chats;
    }
    else
    {
        $chats = [];
        return $chats;
    }
}
?>