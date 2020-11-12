
<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect.


//check whether email is input
if (empty($_POST['email'])){
    echo("<h2>Email shuold not be empty!</h2><br>");
    header("refresh:3;url=browse.php");
    exit();
}
//check whether password is input
if (empty($_POST['password'])){
    echo("<h2>Please enter your password</h2><br>");
    header("refresh:3;url=browse.php");
    exit();
}

session_start();
$username=$_POST['email'];
$password=$_POST['password'];

//select username and password from database to match
$link=mysqli_connect('localhost','root','','auctiondata');
$query="SELECT * from users where users.email='".$username."'";
$result=mysqli_query($link,$query);
$user_data=mysqli_fetch_assoc($result);
$user_password=$user_data['password'];

//check whether username is in database
if (empty($user_data)){
    echo "User not registered";
    header("refresh:5;url=register.php");
    mysqli_close($link);
    exit();
}

//check whether password is correct
if ($password===$user_password){
	echo " Login succesful! Jumping in 3s";
	$_SESSION['logged_in'] = true;
  $_SESSION['username']=$_POST['email'];
	$_SESSION['account_type'] = $user_data['account_type'];
	header ("refresh:3;url=browse.php");
}
else{
	echo "Wrong password.<br>
				Please try again!<br>";
	header("refresh:2;url=browse.php");
	mysqli_close($link);
	exit();

}




?>
