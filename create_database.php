<!--Create a database if there is none-->
<?php
$link = new mysqli('localhost', 'root', '');
$create_database="CREATE  DATABASE IF NOT EXISTS  auctiondata";
$link->query($create_database);
$link->close();
?>

<!--Create a user table-->
<?php
$link=mysqli_connect('localhost', 'root', '', 'auctiondata');
$create_users = "CREATE TABLE if not exists USERS (
    email VARCHAR(50) NOT NULL PRIMARY KEY,
    password VARCHAR(12) NOT NULL,
    account_type VARCHAR(10) NOT NULL)";
if (mysqli_query($link, $create_users)){ //check error!
}
else {
    echo mysqli_error($link);
}
$link->close();
?>
<!--You can create any other tables below-->
