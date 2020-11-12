<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation
// options.


//check whether email is input
if (empty($_POST['email'])){
    echo("<h2>email missing</h2><br>");
    header("refresh:3;url=register.php");
    exit();
}

//check whether email is valid
$regex= '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
if (!preg_match($regex,$_POST['email'])){
  echo("<h2>Invalid email!</h2><br>");
  header("refresh:3;url=register.php");
  exit();
}
//check whether password is input
if (empty($_POST['password'])){
    echo("<h2>please type your password</h2><br>");
    header("refresh:3;url=register.php");
    exit();
}

//check whether password has suitable length
if (strlen($_POST['password'])<6){
  echo("<h2>Password is too short!</h2><br>");
  header("refresh:3;url=register.php");
  exit();
}
if (strlen($_POST['password'])>18){
  echo("<h2>Password is too long!</h2><br>");
  header("refresh:3;url=register.php");
  exit();
}

//check whether confirmationpassword is input
if (empty($_POST['passwordConfirmation'])){
    echo("<h2>please repeate your password</h2><br>");
    header("refresh:3;url=register.php");
    exit();
}

//double check the password
if ($_POST['passwordConfirmation']!==$_POST['password']){
    echo ("<h2>password do not match, try again</h2>");
    header("refresh:3;url=register.php");
    exit();
}
else{
  $_SERVER['REQUEST_METHOD']=='POST';
  $email=$_POST['email'];
  $account_type=$_POST['accountType'];
  $password=$_POST['password'];
}

//POST value to database
$link=mysqli_connect('localhost', 'root', '', 'auctiondata');
$query="Insert into USERS (email, account_type, password) VALUES ('$email', '$account_type', '$password' )";
if (mysqli_query($link, $query)){
    echo "Information succesfully added to the USER table";
      header("refresh:3;url=browse.php");
} else{
    if (mysqli_errno($link)==1062){
        echo "<h2>Email already registered!</h2><br>";
        header("refresh:3;url=register.php");
        exit();
    }
    else {
        echo $link -> error;
    }
}

?>
