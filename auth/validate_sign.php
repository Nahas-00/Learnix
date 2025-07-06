<?php
    require '../utils/connect.php';

    if(isset($_POST['submit'])){
        $email=$_POST['email'];
        $password=$_POST['password'];
        $confirmpass=$_POST['confirmpassword'];
        $username=$_POST['username'];

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);



        if($email== '' || $password == '' || $confirmpass == '' || $username == ''){
            $msg= " Enter all fields";
            $title= 'Warning';
            
        }else if($password != $confirmpass){
            $msg = "Password doesnt match";
            $title= 'Error';
        }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $msg = "Invalid Email !";
            $title= 'Error';
        }else if(strlen($password)< 5){
            $msg= "Password must be at least 5 characters";
            $title= 'Warning';
        }else if(!preg_match('/[0-9]/' , $password)){
            $msg= "Password must contain atleast 1 number";
            $title= 'Warning';
        }else{
            $stmt = $pdo->prepare("select *from users where email= :email LIMIT 1");
            $stmt->execute(['email'=>$email]);
            $user=$stmt->fetch();

            if($user){
                $msg = "Email already exists !";
                $title= 'Warning';
            }else{

                if($username){
                    $msg="Username already taken.";
                    $title= 'warning';
                }else{
                $hash_pass = password_hash($password,PASSWORD_DEFAULT);
                $stmt=$pdo->prepare("insert into users (email,password,username) values(?,?,?)");
                $insertion = $stmt->execute([$email,$hash_pass,$username]);

                if($insertion){
                    $msg = " Redirecting..! Log in to Continue";
                    $title= 'Success';
                }else{
                    $msg = "Sign up Failed!";
                    $title= 'Error';
                }
            }

        }
        }
    }
?>