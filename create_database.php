
<?php
header('Content-type:text/html;charset=utf-8');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comp0022";
$user_table="user";
$item_table="Item";
$category_table="category";
$bid_table="biddingHistory";
$watchlist_table="watchlist";
$auction_table="auction";


//Create a database if there is none
$link = new mysqli($servername, $username, $password);
$create_database="CREATE  DATABASE IF NOT EXISTS $dbname";
mysqli_query($link, $create_database);
if(mysqli_errno($link)){     //check error!
  exit(mysqli_error($link));
}
mysqli_close($link);
?>

<!--Create user table-->
<?php
$link=mysqli_connect($servername, $username, $password, $dbname);
$create_user = "CREATE TABLE if not exists $user_table (
    email VARCHAR(30) NOT NULL PRIMARY KEY,
    password VARCHAR(30) NOT NULL,
    role VARCHAR(10) NOT NULL)
    ENGINE=InnoDB DEFAULT CHARSET=utf8;";
mysqli_query($link, $create_user);
  if(mysqli_errno($link)){             //check error!
    exit(mysqli_error($link));
  }

mysqli_close($link);
?>
<!--Create item table-->
<?php
$link=mysqli_connect($servername, $username, $password, $dbname);
$create_item = "CREATE TABLE if not exists $item_table (
    itemID INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    sellerEmail VARCHAR(30) NOT NULL,
    description VARCHAR(30) NOT NULL,
    title VARCHAR(10) NOT NULL,
    CONSTRAINT `item_1` FOREIGN KEY (`sellerEmail`) REFERENCES `$user_table` (`email`))
    ENGINE=InnoDB DEFAULT CHARSET=utf8;";
mysqli_query($link, $create_item);
  if(mysqli_errno($link)){
    exit(mysqli_error($link));
  }
mysqli_close($link);
?>

<!--Create category table-->
<?php
$link=mysqli_connect($servername, $username, $password, $dbname);
$create_category = "CREATE TABLE if not exists $category_table (
    categoryID INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(30) NOT NULL)
    ENGINE=InnoDB DEFAULT CHARSET=utf8;";
mysqli_query($link, $create_category);
  if(mysqli_errno($link)){     //check error!
    exit(mysqli_error($link));
  }
mysqli_close($link);
?>

<!--Create biddingHistory table-->
<?php
$link=mysqli_connect($servername, $username, $password, $dbname);
$create_bid = "CREATE TABLE if not exists $bid_table (
    itemID INT(6) NOT NULL ,
    buyerEmail VARCHAR(30) NOT NULL ,
    biddingTime datetime NOT NULL ,
    bidPrice double NOT NULL,
    PRIMARY KEY(itemID,buyerEmail,biddingTime),
    CONSTRAINT `bid_1` FOREIGN KEY (`itemID`) REFERENCES `$item_table` (`itemID`),
    CONSTRAINT `bid_2` FOREIGN KEY (`buyerEmail`) REFERENCES `$user_table` (`email`))
    ENGINE=InnoDB DEFAULT CHARSET=utf8;";
mysqli_query($link, $create_bid);
  if(mysqli_errno($link)){     //check error!
    exit(mysqli_error($link));
  }
mysqli_close($link);
?>

<!--Create watchlist table-->
<?php
$link=mysqli_connect($servername, $username, $password, $dbname);
$create_watchlist = "CREATE TABLE if not exists $watchlist_table (
    itemID INT(6) NOT NULL,
    buyerEmail VARCHAR(30) NOT NULL,
    PRIMARY KEY(itemID, buyerEmail),
    CONSTRAINT `watch_1` FOREIGN KEY (`BuyerEmail`) REFERENCES `$user_table` (`email`),
    CONSTRAINT `watch_2` FOREIGN KEY (`ItemID`) REFERENCES `$item_table` (`itemID`))
    ENGINE=InnoDB DEFAULT CHARSET=utf8;";
mysqli_query($link, $create_watchlist);
  if(mysqli_errno($link)){     //check error!
    exit(mysqli_error($link));
  }
mysqli_close($link);
?>

<!--Create auction table-->
<?php
$link=mysqli_connect($servername, $username, $password, $dbname);
$create_auction = "CREATE TABLE if not exists $auction_table (
  itemID int(11) NOT NULL PRIMARY KEY,
  sellerEmail varchar(30) NOT NULL,
  categoryID int(11) NOT NULL,
  startingPrice double NOT NULL,
  currentPrice double NOT NULL,
  reservePrice double NOT NULL,
  endDate date NOT NULL,
  CONSTRAINT `auction_1` FOREIGN KEY (`itemID`) REFERENCES `$item_table` (`itemID`),
  CONSTRAINT `auction_2` FOREIGN KEY (`sellerEmail`) REFERENCES `$user_table` (`email`),
  CONSTRAINT `auction_3` FOREIGN KEY (`categoryID`) REFERENCES `$category_table` (`categoryID`))
  ENGINE=InnoDB DEFAULT CHARSET=utf8;";
mysqli_query($link, $create_auction);
  if(mysqli_errno($link)){     //check error!
    exit(mysqli_error($link));
  }
mysqli_close($link);
?>
