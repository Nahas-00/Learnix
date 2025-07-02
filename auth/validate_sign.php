<?php
    require '../utils/connect.php';

    if(isset($_POST['submit'])){
        $email=$_POST['email'];
        $password=$_POST['password'];
        $confirmpass=$_POST['confirmpassword'];
        $username=$_POST['username'];

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);



        if($email== '' || $password == '' || $confirmpass == ''){
            $msg= "Invalid! Enter all fields";
            
        }else if($password != $confirmpass){
            $msg = "Password doesnt match";
        }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $msg = "Invalid Email !";
        }else{
            $stmt = $pdo->prepare("select *from users where email= :email LIMIT 1");
            $stmt->execute(['email'=>$email]);
            $user=$stmt->fetch();

            if($user){
                $msg = "Email already exists !";
            }else{
                $hash_pass = password_hash($password,PASSWORD_DEFAULT);
                $stmt=$pdo->prepare("insert into users (email,password,username) values(?,?,?)");
                $insertion = $stmt->execute([$email,$hash_pass,$username]);

                if($insertion){
                    $msg = "Successfull! Log in to Continue";
                }else{
                    $msg = "Sign up Failed!";
                }
            }
        }
    }
?>