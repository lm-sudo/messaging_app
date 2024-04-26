<?php

function getConversation($user_id, $conn)
{
    $sql = "SELECT * FROM conversations WHERE user_1 = :user1 OR user_2 = :user1
    ORDER BY conversation_id DESC";

    $res = $conn->prepare($sql);
    $res->execute([$user_id]);

    if($res->rowCount() > 0)
    {
        $conversations = $res->fetchAll();

        $user_data = [];

        foreach($conversations as $conversation)
        {
            if($conversation['user_1'] == $user_id)
            {
                $sql = "SELECT * FROM users WHERE user_id = :user";
                $res2 = $conn->prepare($sql);
                $res2->execute([$conversation['user_2']]);
            }
            else
            {
                $sql = "SELECT * FROM users WHERE user_id = :user";
                $res2 = $conn->prepare($sql);
                $res2->execute([$conversation['user_1']]);
            }

            $all_conversations = $res2->fetchAll();

            array_push($user_data,$all_conversations[0]);
        }

        return $user_data;
    }
    else
    {
        $conversations = [];
        return $conversations;
    }
}
?>