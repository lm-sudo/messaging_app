<?php

session_start();

if(isset($_SESSION['username']))
{
    if(isset($_POST['key']))
    {
        include '../db.conn.php';

        $key = "%{$_POST['key']}%";

        $sql = "SELECT * FROM users WHERE username ilike :key or name ilike :key";
        $res = $conn->prepare($sql);
        $res->execute([$key]);

        if($res->rowCount() > 0)
        {
            $users = $res->fetchAll();

            foreach($users as $user) 
            {
                if ($user['user_id'] == $_SESSION['user_id']) continue;
        ?>
        <li class="list-group-item">
		<a href="chat.php?user=<?=$user['username']?>" class="d-flex justify-content-between align-items-center p-2">
			<div class="d-flex align-items-center">
			    <img src="uploads/<?=$user['p_p']?>" class="w-10 rounded-circle">
			    <h3 class="fs-xs m-2">
			    	<?=$user['name']?>
			    </h3>            	
			</div>
		 </a>
	   </li>
       <?php } }
       else 
       { ?>
       <div class="alert alert-info text-center">
		   <i class="fa fa-user-times d-block fs-big"></i>
           The user "<?=htmlspecialchars($_POST['key'])?>"
           could not be found.
		</div>
        <?php
        }
    }
}
else
{
    header("Locaton: ../../index.php");
    exit;
}
?>