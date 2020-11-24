<?php
$servername = "localhost";
$username = "root";
$password = '';
$dbname = "comp0022";

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//check whether the databse exsits
$sql_check_db_existence = "SELECT SCHEMA_NAME
FROM INFORMATION_SCHEMA.SCHEMATA
WHERE SCHEMA_NAME = '$dbname'";
$databse_num = ($conn->query($sql_check_db_existence))->num_rows;

//if there is no such database, then create one
if($databse_num === 0){
    // Create database
    $conn = new mysqli($servername, $username, $password);
    $sql_db = "CREATE DATABASE $dbname";
    if ($conn->query($sql_db) === TRUE) {
        
    } else {
        echo "Error creating database: " . $conn->error . ".  ";
    }
    $conn->close();

    //Create tables
    $conn = new mysqli($servername, $username, $password, $dbname);
    $sql_tables = "CREATE TABLE `User` (
    `email` varchar(30) NOT NULL,
    `password` varchar(30) NOT NULL,
    `role` enum('seller','buyer') NOT NULL,
    PRIMARY KEY (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    CREATE TABLE `Item` (
    `itemID` int(11) NOT NULL AUTO_INCREMENT,
    `sellerEmail` varchar(30) NOT NULL,
    `title` varchar(30) NOT NULL,
    `description` varchar(200) NOT NULL,
    PRIMARY KEY (`itemID`),
    KEY `sellerEmail` (`sellerEmail`),
    CONSTRAINT `item_ibfk_1` FOREIGN KEY (`sellerEmail`) REFERENCES `User` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    CREATE TABLE `Category` (
    `categoryID` int(11) NOT NULL AUTO_INCREMENT,
    `description` varchar(30) NOT NULL,
    PRIMARY KEY (`categoryID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    CREATE TABLE `BiddingHistory` (
    `ItemID` int(11) NOT NULL,
    `buyerEmail` varchar(30) NOT NULL,
    `biddingTime` datetime NOT NULL,
    `bidPrice` double NOT NULL,
    PRIMARY KEY (`ItemID`,`buyerEmail`,`biddingTime`),
    KEY `buyerEmail` (`buyerEmail`),
    CONSTRAINT `biddinghistory_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `Item` (`itemID`),
    CONSTRAINT `biddinghistory_ibfk_2` FOREIGN KEY (`buyerEmail`) REFERENCES `User` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    CREATE TABLE `WatchList` (
    `ItemID` int(11) NOT NULL,
    `BuyerEmail` varchar(30) NOT NULL,
    PRIMARY KEY (`ItemID`,`BuyerEmail`),
    KEY `BuyerEmail` (`BuyerEmail`),
    CONSTRAINT `watchlist_ibfk_1` FOREIGN KEY (`BuyerEmail`) REFERENCES `User` (`email`),
    CONSTRAINT `watchlist_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `Item` (`itemID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    CREATE TABLE `Auction` (
    `itemID` int(11) NOT NULL,
    `sellerEmail` varchar(30) NOT NULL,
    `newcategoryID` int(11) NOT NULL,
    `categoryID` int(11) NOT NULL,
    `startingPrice` double NOT NULL,
    `currentPrice` double NOT NULL,
    `reservePrice` double NULL,
    `endDate` datetime NOT NULL,
    PRIMARY KEY (`itemID`),
    KEY `sellerEmail` (`sellerEmail`),
    KEY `categoryID` (`categoryID`),
    CONSTRAINT `auction_ibfk_1` FOREIGN KEY (`itemID`) REFERENCES `Item` (`itemID`),
    CONSTRAINT `auction_ibfk_2` FOREIGN KEY (`sellerEmail`) REFERENCES `User` (`email`),
    CONSTRAINT `auction_ibfk_3` FOREIGN KEY (`categoryID`) REFERENCES `Category` (`categoryID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    CREATE TABLE if not exists `newCategory`(".
    "`newcategoryID` int(11) NOT NULL AUTO_INCREMENT,`newdescription` varchar(10) NOT NULL,`amount` int(11) NOT NULL, ".
    "Primary key(`newcategoryID`)".
    ")ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    if ($conn->multi_query($sql_tables) === TRUE) {
    } else {
        echo "Error creating tables: " . $conn->error;
    }

    $conn->close();
}else{
    $conn->close();
}
?>
<?php

// $conn = new mysqli($servername, $username, $password, $dbname);
// $sql_check_category = "SELECT * FROM Category;";
// $result_check_category = $conn->query($sql_check_category);
// if($result_check_category==null){
//   $nums_of_category=0;
// }else {
// $nums_of_category = $result_check_category->num_rows;
// }
// if($nums_of_category == 0){
//     // add data to Catergory
//     $default_category = array("Electronic Device", "Household Commodity", "Jewellery", "House", "Art Work", "Fashion", "Car", "Book", "Other");
//     for($i = 0; $i < count($default_category); $i ++){
//         $temp = $default_category[$i];
//         $sqli = "INSERT INTO Category "."(description) "."VALUES "."('$temp')";
//         $ins = $conn->query($sqli);
//         if(!$ins)
//         {
//         exit('Can\'t insert new Category: '.mysqli_error($conn));
//         }
//     }
//     // $sqli = "INSERT INTO `Category` (description) VALUES ('car')";
//     if (mysqli_query($conn, $sqli)){
//         echo '1';
//     }else {
//             echo '2';
//             echo $conn -> error;
//         }
// }
// Create connection again
$conn = new mysqli($servername, $username, $password,$dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
$sql_check_category = "SELECT * FROM Category;";
$result_check_category = $conn->query($sql_check_category);
$nums_of_category = $result_check_category->num_rows;

if($nums_of_category == 0){
    // add data to Catergory
    $default_category = array("Electronic Device", "Household Commodity", "Jewellery", "House", "Art Work", "Fashion", "Car", "Book", "Other");
    for($i = 0; $i < count($default_category); $i ++){
        $temp = $default_category[$i];
        $sql = "INSERT INTO Category ".
        "(description) ".
        "VALUES ".
        "('$temp')";

        $ins = $conn->query($sql);
        if(!$ins)
        {
        exit('Can\'t insert new Category: '.mysqli_error($conn));
        }
    }
}

$conn->close();
 ?>
