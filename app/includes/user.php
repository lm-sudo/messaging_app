<?php

function getUser($username, $conn) 
{
    $sql = "SELECT * FROM users WHERE username = :username";
    $res = $conn->prepare($sql);
    $res->execute([$username]);

    if($res->rowCount() === 1)
    {
        $user = $res->fetch();
        return $user;
    }
    else
    {
        $user = [];
        return $user;
    }
}
?>