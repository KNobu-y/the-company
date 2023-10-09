<?php

require_once "Database.php"; 

class User extends Database
{
    public function store($request)
    { 
        $first_name = ucwords(strtolower($request['first_name']));
        // $_POST['first_name]
        $last_name = ucwords(strtolower($request['last_name']));
        $username = $request['username'];
        $password = password_hash($request['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, username, password) 
        VALUES ('$first_name', '$last_name', '$username', '$password')";

        if($this->conn->query($sql))
        {
            header('location: ../views');  //go to views/index.php 
            exit;
        }  
        else
        {
            die('Error creating new user: ' . $this->conn->error);
        }
    }

    public function login($request)
    {
        $username = $request['username'];
        $password = $request['password'];

        $sql = "SELECT * FROM users WHERE username = '$username'";

        $result = $this->conn->query($sql);

        if($result->num_rows == 1) // If it's one input(record)(If the user exits)
        {
            $user = $result->fetch_assoc(); // get data from database table and save it in $user

            /*
            $username = "spidey"
            $user = 
            [
                'id' => 6,
                'first_name' => 'Peter',
                'last_name' => 'parker'
                'password' => '11111111',
                'photo' => ''
            ]

            $user['id] = 6
            $user['first_name'] = 'Peter'
            $user['password'] = '1111111'

            */

            if(password_verify($password, $user['password'])) // If the password is correct
            {
                // Create session variables for future use    keep us login
                session_start(); // so that we can successly log in
                $_SESSION['id']         = $user['id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['full_name']  = ucwords(strtolower($user['first_name'])) . " " .$user['last_name'];  // all character should be lowercase. But using ucwords it makse first letters uppercase.

                header('location: ../views/dashboard.php'); // If the password correct, this is the process workingß
                exit;
            }
            else
            {
                die('Incorrect Password');
            }
        }
        else
        {
            ('Username not found.');
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header('location: ../views');
        exit;
    }

    public function getAllUsers()//to get all records of user
    {
        $sql = "SELECT id, first_name, last_name, username, photo FROM users";

        if($result = $this->conn->query($sql))// If the query goes success
        {
            return $result;
        }
        else
        {
            die('Error retrieving all users: ' . $this->conn->error);
        }
    }

    public function getUser($id)
    {
        $sql = "SELECT * FROM users WHERE id = $id"; /*to get specific user*/

        if($result = $this->conn->query($sql))
        {
            return $result->fetch_assoc();
        }
        else
        {
            die('Error retrieving the user: ' . $this->conn->error);
        }
    }

    public function update($request, $files)//get all the values and save it thosse properties below
    {
        session_start();
        $id          = $_SESSION['id'];
        $first_name  = $request['first_name'];
        $last_name   = $request['last_name'];
        $username    = $request['username'];
        $photo       = $files['photo']['name'];
        $tmp_photo   = $files['photo']['tmp_name'];


        $first_name = ucwords(strtolower($first_name));
        $last_name = ucwords(strtolower($last_name));

        $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username' WHERE id =$id";

        if($this->conn->query($sql)){
            $_SESSION['username'] = $username;
            $_SESSION['full_name'] = "$first_name $last_name";

            # If there is a photo to upload, save it to yje database and save the file to assets/images folder
            if($photo)
            {
                $sql = "UPDATE users SET photo = '$photo' WHERE id = $id";
                $destination = "../assets/images/$photo";

                // Save the image to database
                if($this->conn->query($sql))
                {
                   // Save the file to assets/image
                   if(move_uploaded_file($tmp_photo, $destination))
                   {
                        header('location: ../views/dashboard.php');
                        exit;
                   }
                   else
                   {
                        die('Error moving the photo.');
                   }
                }
                else
                {
                    die('Error uploading the photo: ' . $this->conn->error);
                }
            }
            header('location: ../views/dashboard.php');
            exit;
        }
        else
        {
            die('Error updating the user: ' . $this->conn->error);
        }
    }

    public function delete()
    {
        session_start();
        $id = $_SESSION['id'];

        $sql = "DELETE FROM users WHERE id = $id";

        if($this->conn->query($sql))
        {
            $this->logout();
        }
        else
        {
            die('Error deleting your account: ' . $this->conn->error);
        }
    }
}

?>
