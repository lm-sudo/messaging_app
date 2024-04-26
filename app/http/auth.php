<?php
session_start();

//Check that a username and password have been sent via POST.
if (isset($_POST['username']) && isset($_POST['password']))
{
    //Grab the posted username and password and capture them in a variable.
    $username = $_POST['username'];
    $password = $_POST['password'];

    //Check that what is posted is not empty.
    if(empty($username) && empty($password))
    {
        //If the variables are empty, notify the user with error message.
        $em = "Both a username and password are required.";
        header("Location: ../../index.php?error=$em");
    }
    elseif(empty($username))
    {
        //If only username is empty, notify the user with error message.
        $em = "A username is required.";
        header("Location: ../../index.php?error=$em");
    }
    elseif(empty($password))
    {
        //If only password is empty, notify the user with error message.
        $em = "A password is required.";
        header("Location: ../../index.php?error=$em"); 
    }
    else
    {
        //Both username and password are non-empty so check validity via database check.
        include "../db.conn.php";

        //Query to pull user specific data.
        $user_exists = "SELECT * FROM users WHERE username ilike :username";

        //Replace parameters with relevant variables.
        $res = $conn->prepare($user_exists);
        //Execute parameterized query.
        $res->execute([$username]);

        //Check that results contain exactly one row.
        if($res->rowCount() === 1)
        {
            //Return row results.
            $user = $res->fetch();

            //Check that username posted is strictly equal to a username in the database.
            if($user['username'] === $username)
            {
                //Verify encrypted password.
                if(password_verify($password, $user['password']))
                {
                    //Successfully logged in, therefore create session.
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['user_id']= $user['user_id'];
                    //Now logged in, redirect to home.
                    header('Location: ../../home.php');
                }
                else
                {
                    //If password does not match the password in our db for given username
                    //return an error message.
                    $em = "Incorrect username or password.";
                    header("Location: ../../index.php?error=$em");
                }
            }
            else
            {
                $em = "Incorrect username or password.";
                //Send back to login page w/ error message.
                header("Location: ../../index.php?error=$em");
            }
        }
        else
        {
            echo "You've encountered an error.";
            echo "<br>";
            echo $_POST['username'];
            echo "<br>";

            echo $user_exists;
        }
    }
}
else
{
    header("Location: ../../index.php");
    exit;
}
?>