<?php

function opened($id_1, $conn, $chats)
{
    foreach($chats as $chat)
    {
        if($chat['opened'] == 'f')
        {
            $opened = 't';
            $chat_id = $chat['chat_id'];

            $sql = "UPDATE chats SET opened = :opened WHERE from_id = :chat_id AND chat_id = :chat_id";
            $res = $conn->prepare($sql);
            $res->execute([$opened, $id_1, $chat_id]);
        }
    }
}
?>