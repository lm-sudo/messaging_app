<?php 

//Check that all three necessary fields are passed.
if(isset($_POST['username']) &&
   isset($_POST['password']) && 
   isset($_POST['name']))
{
    //Get data from POST request and store them in variables.
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];

    //Perform a simple validation to ensure alphanumeric characters only.
    /*if(!preg_match('/^\w{5,}$/', $username))
    {
        $em = "Username's can only contain letters and numbers.";
        header("Location: ../../index.php?error=$em");
        exit;
    }*/
    
    //Establish database connection.
    include '../db.conn.php';

    //URL data format:
    $data = 'name='.$name.'&username='.$username;

    //Check if any required fields are missing before querying database.
    if(empty($name)){
        $em = "A name is required to sign up.";
        header("Location: ../../signup.php?em=$em&$data");
        exit;
    }
    elseif(empty($username))
    {
        $em = "A username is required to sign up.";
        header("Location: ../../signup.php?em=$em&$data");
        exit;
    }
    elseif(empty($password))
    {   
        $em = "A password is required to sign up.";
        header("Location: ../../signup.php?em=$em&$data");
        exit;
    }
    else
    {
        //Query to check if username already exists:
        $sql = "SELECT * FROM users where username ilike :username";
        $res = $conn->prepare($sql);
        $res->execute([$username]);

        //If username already exists return error.
        if($res->rowCount() > 0)
        {
            $em = "The username ($username) is already taken. Please select another username.";
            header("Location: ../../signup.php?em=$em");
            exit;
        }
        else
        {
            //If the user passed a file to be used as a profile picture format it.
            if(isset($_FILES['pp']))
            {
                $img_name =$_FILES['pp']['name'];
                $tmp_name = $_FILES['pp']['tmp_name'];
                $error = $_FILES['pp']['error'];

                //If no errors occurred during file upload proceed.
                if($error === 0)
                {
                    //Get the extension of the file uploaded.
                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                    //Convert the image extension to lowercase letters.
                    $img_ex_lc = strtolower($img_ex);
                    //Create array to contain the allowed file extensions.
                    $allowed_exts = ['jpg','jpeg','png'];
                    //If the file upload contains one of the allowed file extensions.
                    if(in_array($img_ex, $allowed_exts))
                    {
                        //Create the new image name.
                        $new_img_name = $username.'.'.$img_ex_lc;
                        //Define the destination for the user's profile picture.
                        $img_upload_path = "../../uploads/$new_img_name";
                        //Move the user's profile picture to the appropriate path.
                        move_uploaded_file($tmp_name,$img_upload_path);
                    }
                    else
                    {
                        $em = "Files must be .jpeg, .jpg, or .png.";
                        header("Location: ../../signup.php?error=$em&$data");
                        exit;
                    }
                }
            }
            //Create a new password hash using a strong one-way hashing algorithm.
            $password = password_hash($password, PASSWORD_DEFAULT);
            //The user passed a valid profile picture.
            if(isset($new_img_name))
            {
                //Insert new user into database.
                $sql = "INSERT INTO users (name,username,password,p_p) 
                VALUES (:name, :username, :password, :p_p)";
                $res = $conn->prepare($sql);
                $res->execute([$name, $username, $password, $new_img_name]);
            }
            else
            {
                //Insert new user into database.
                $sql = "INSERT INTO users (name, username, password) VALUES(:name, :username, :password)";
                $update = $conn->prepare($sql);
                $update->execute([$name, $username, $password]);
            }

            //Notify user of success and send to index page.
            $sm = "Account successfully created!";
            header("Location: ../../index.php?success=$sm");
            exit;
        }
    }
}
else
{
    //None of the required fields were passed, return to signup w/ error message.
    $em = "Username, password, and name are required for signup.";
    header("Location: ../../signup.php");
    exit;
}
?>