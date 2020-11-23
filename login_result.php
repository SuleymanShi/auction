<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap and FontAwesome CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- Custom CSS file -->
  <link rel="stylesheet" href="css/custom.css">

  <title>[Logging Information] <!--CHANGEME!--></title>
</head>
<body>
<?php include_once("create_database.php")?>
<?php
session_start();
// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect.

// if (!isset($_SESSION['logged_in'])) {
//   echo '<h2 class="my-3 text-center">Please log in first!</h2>';
//   header("refresh:3;url=browse.php");
// }

//check whether email is input
if (empty($_POST['email'])){
    echo '<h2 class="my-3 text-center">Email shuold not be empty!</h2>';
    header("refresh:3;url=browse.php");
    exit();
}
//check whether password is input
elseif (empty($_POST['password'])){
    echo'<h2 class="my-3 text-center">Please enter your password!</h2>';
    header("refresh:3;url=browse.php");
    exit();
}


//select username and password from database to match
$link=mysqli_connect($servername,$username,$password,$dbname);
$query="SELECT * FROM User WHERE User.email='{$_POST['email']}'";
$result=mysqli_query($link,$query);
$user_data=mysqli_fetch_assoc($result);
$user_password=$user_data['password'];

//check whether username is in database
if (empty($user_data)){
    echo '<h2 class="my-3 text-center">User not registered!</h2>';
    header("refresh:5;url=register.php");
    mysqli_close($link);
    exit();
}

//check whether password is correct
if ($_POST['password']===$user_password){
	echo '<h2 class="my-3 text-center">Login succesful! Jumping in 3s!</h2>';
	$_SESSION['logged_in'] = true;
  $_SESSION['username']=$_POST['email'];
	$_SESSION['account_type'] = $user_data['role'];
	header ("refresh:3;url=browse.php");
}
else{
	echo '<h2 class="my-3 text-center">Wrong password.<br>
				Please try again!</h2>';
	header("refresh:2;url=browse.php");
	mysqli_close($link);
	exit();

}


?>
