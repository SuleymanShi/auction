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

  <title>[Register Information] <!--CHANGEME!--></title>
</head>
<body>



  <?php include_once("create_database.php")?>
  <?php

  // TODO: Extract $_POST variables, check they're OK, and attempt to create
  // an account. Notify user of success/failure and redirect/give navigation
  // options.
  /*
  '<h2 class="my-3 text-center">Congratulations! Account has been created</h2>'
  */

  //check whether email is input
  if (empty($_POST['email'])){
      echo '<h2 class="my-3 text-center">email missing</h2>';
      header("refresh:3;url=register.php");
      exit();
  }

  //check whether email is valid
  $regex= '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
  if (!preg_match($regex,$_POST['email'])){
    echo  '<h2 class="my-3 text-center">Invalid email!</h2>';
    header("refresh:3;url=register.php");
    exit();
  }
  //check whether password is input
  if (empty($_POST['password'])){
      echo '<h2 class="my-3 text-center">please type your password</h2>';
      header("refresh:3;url=register.php");
      exit();
  }

  //check whether password has suitable length
  if (strlen($_POST['password'])<6){
    echo'<h2 class="my-3 text-center">Password is too short</h2>';
    header("refresh:3;url=register.php");
    exit();
  }
  if (strlen($_POST['password'])>18){
    echo'<h2 class="my-3 text-center">Password is too long</h2>';
    header("refresh:3;url=register.php");
    exit();
  }

  //check whether confirmationpassword is input
  if (empty($_POST['passwordConfirmation'])){
      echo'<h2 class="my-3 text-center">Please repeate your password</h2>';
      header("refresh:3;url=register.php");
      exit();
  }

  //double check the password
  if ($_POST['passwordConfirmation']!==$_POST['password']){
      echo '<h2 class="my-3 text-center">password do not match, try again</h2>';
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
  $link=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, 'comp0022');
  $query="Insert into USERS (email, account_type, password) VALUES ('$email', '$account_type', '$password' )";


  if (mysqli_query($link, $query)){
      echo '<h2 class="my-3 text-center">Congratulations! Account has been created</h2>';
        header("refresh:3;url=browse.php");
  } else{
      if (mysqli_errno($link)==1062){
          echo '<h2 class="my-3 text-center">Sorry! Account has been registered already</h2>';
          header("refresh:3;url=register.php");
          exit();
      }
      else {
          echo $link -> error;
      }
  }

  ?>


</body>
