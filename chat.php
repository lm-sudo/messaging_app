<?php
//Begin session.
session_start();
//If the user is logged in.
if(isset($_SESSION['username']))
{
    //Add includes.
    include 'app/db.conn.php';
    include 'app/includes/user.php';
    include 'app/includes/chat.php';
    include 'app/includes/opened.php';
    include 'app/includes/timeAgo.php';

    if(!isset($_GET['user']))
    {
        header("Location: home.php");
        exit;
    }
    //Get user data.
    $chatWith = getUser($_GET['user'], $conn);
    //If no user data send to home page.
    if(empty($chatWith))
    {
        header("Location: home.php");
        exit;
    }
    //Get chats between the logged in user and the chatwith user.
    $chats = getChats($_SESSION['user_id'], $chatWith['user_id'], $conn);

    opened($chatWith['user_id'], $conn, $chats);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="w-400 shadow p-4 rounded">
    	<a href="home.php" class="fs-4 link-dark">&#8592;</a>
    	   <div class="d-flex align-items-center">
    	   	    <img src="uploads/<?=$chatWith['p_p']?>" class="w-15 rounded-circle">
                <h3 class="display-4 fs-sm m-2">
                    <?=$chatWith['name']?> <br>
                    <div class="d-flex align-items-center" title="online">
                    <?php
                        if(last_seen($chatWith['last_seen']) == "Active")
                        {
                    ?>
                        <div class="online"></div>
                        <small class="d-block p-1">Online</small>
                    <?php
                        }
                        else
                        {
                    ?>  
                        <small class="d-block p-1">
                            Last Seen:
                            <?=last_seen($chatWith['last_seen'])?>
                        </small>
                    <?php
                        }
                    ?>
                    </div>
                </h3>
            </div>

            <div class="shadow p-4 rounded d-flex flex-column mt-2 chat-box" id="chatBox">
            <?php
                //Chats exist between the two users.
                if(!empty($chats))
                {
                    foreach($chats as $chat)
                    {
                        //If chats are those from the logged in user, show them on the right hand side.
                        if($chat['from_id'] == $_SESSION['user_id'])
                        {
                            ?>
                        <p class="rtext align-self-end border rounded p-2 mb-1">
                            <?=$chat['message']?>
                            <small class="d-block">
                                <?=$chat['created_at']?>
                            </small>
                        </p>
                        <?php
                        //If chats are those from the chat with user, show them on the left hand side.
                        }
                        else
                        {
                            ?>
                        <p class="ltext border rounded p-2 mb-1">
                            <?=$chat['message']?>
                            <small class="d-block">
                                <?=$chat['created_at']?>
                            </small>
                        </p>
                        <?php
                        }
                    }
                }
                else
                { ?>
                <div class="alert alert-info text-center">
                    <i class="fa fa-comments d-block fs-big">
                        No messages yet, start the conversation.
                    </i>
                </div>
                <?php
                } ?>
            </div>
            <div class="input-group mb-3">
                <textarea cols="3" id="message" class="form-control"></textarea>
                <button class="btn btn-primary" id="sendBtn">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </div>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    //Define function that pulls the user to the most recent message.
    var scrollDown = function(){
        let chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    scrollDown();

    $(document).ready(function()
    {
        $("#sendBtn").on('click', function()
        {
            //Get the message to be sent.
            message = $('#message').val();
            if(message == "") return;
            //Send it via POST request for insertion into database.
            $.post("app/ajax/insert.php",
            {
                message: message,
                to_id: <?=$chatWith['user_id']?>
            },
            function(data, status)
            {
                //Clear the user's message box upon send, and update their chat box.
                $("#message").val("");
                $("#chatBox").append(data);
                scrollDown();
            });
        });
        //Update the user's last seen time.
        let lastSeenUpdate = function()
        {
            $.get("app/ajax/update_last_seen.php");
        }
        lastSeenUpdate();
        setInterval(lastSeenUpdate, 10000);
        //Define a function to receive the chatwith user's messages.
        let fetchData = function()
        {
            $.post("app/ajax/getMessage.php",
                {
                    id_2: <?=$chatWith['user_id']?>
                },
                function(data, status)
                {
                    //Add the new message to the chat box.
                    $("#chatBox").append(data);
                    if(data != "") scrollDown();
                });
        }

        fetchData();
        setInterval(fetchData, 500);
    });
</script>
</body>
</html>
<?php
}
else
{
    header("Location: index.php");
    exit;
}
?>