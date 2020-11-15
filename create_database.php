
<?php
header('Content-type:text/html;charset=utf-8');
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASSWORD','');

//Create a database if there is none
$link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);
$create_database="CREATE  DATABASE IF NOT EXISTS  comp0022";
mysqli_query($link, $create_database);
if(mysqli_errno($link)){     //check error!
  exit(mysqli_error($link));
}
// $link->query($create_database);
mysqli_close($link);
?>

<!--Create a user table-->
<?php
$link=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, 'comp0022');
$create_users = "CREATE TABLE if not exists USERS (
    email VARCHAR(30) NOT NULL PRIMARY KEY,
    password VARCHAR(30) NOT NULL,
    account_type VARCHAR(10) NOT NULL)";
mysqli_query($link, $create_users);
  if(mysqli_errno($link)){     //check error!
    exit(mysqli_error($link));
  }

mysqli_close($link);
?>
<!--You can create any other tables below-->
<?php
$link=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, 'comp0022');
$create_auction = "CREATE TABLE if not exists AUCTIONS (
    itemID INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(30) NOT NULL,
    categoryID INT NOT NULL,
    startingPrice FLOAT NOT NULL,
    currentPrice FLOAT NOT NULL,
    reservePrice FLOAT NOT NULL,
    endDate date NOT NULL)";
mysqli_query($link, $create_auction);
  if(mysqli_errno($link)){     //check error!
    exit(mysqli_error($link));
  }

mysqli_close($link);
?>

<?php
$link=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, 'comp0022');
$create_item = "CREATE TABLE if not exists ITEMS (
    itemID INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(30) NOT NULL,
    itemTitle VARCHAR(30) NOT NULL,
    Description VARCHAR(200) NOT NULL,
    CONSTRAINT `ITEMS_ibfk_1` FOREIGN KEY (`email`) REFERENCES `USERS` (`email`)
    -- CONSTRAINT `ITEMS_ibfk_1` FOREIGN KEY (`email`) REFERENCES `USERS` (`email`)
  )";

mysqli_query($link, $create_item);
  if(mysqli_errno($link)){     //check error!
    exit(mysqli_error($link));
  }

mysqli_close($link);
?>
