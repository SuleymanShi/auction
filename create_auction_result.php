<?php include_once("header.php")?>
<?php include_once("create_database.php")?>
<div class="container my-5">

<?php

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */


/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'],
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */
if (empty($_POST['auctionTitle'])){
    echo '<h2 class="my-3 text-center">AuctionTitle missing</h2>';
    header("refresh:3;url=create_auction.php");
    exit();
}
elseif (empty($_POST['auctionDetails'])){
    echo '<h2 class="my-3 text-center">AuctionDetails missing</h2>';
    header("refresh:3;url=create_auction.php");
    exit();
}
elseif (empty($_POST['auctionCategory'])){
    echo '<h2 class="my-3 text-center">Please choose at least one category</h2>';
    header("refresh:3;url=create_auction.php");
    exit();
}
elseif (empty($_POST['auctionStartPrice'])){
    echo '<h2 class="my-3 text-center">auctionStartPrice missing</h2>';
    header("refresh:3;url=create_auction.php");
    exit();
}
elseif (empty($_POST['auctionReservePrice'])){
    echo '<h2 class="my-3 text-center">auctionReservePrice missing</h2>';
    header("refresh:3;url=create_auction.php");
    exit();
}
elseif (empty($_POST['auctionEndDate'])){
    echo '<h2 class="my-3 text-center">Please choose an auctionEndDate</h2>';
    header("refresh:3;url=create_auction.php");
    exit();
}

$sellerEmail=$_SESSION['username'];
$auctionTitle=$_POST['auctionTitle'];
$auctionDetails=$_POST['auctionDetails'];
$auctionCategory=$_POST['auctionCategory'];
$auctionStartPrice=$_POST['auctionStartPrice'];
$auctionReservePrice=$_POST['auctionReservePrice'];
$auctionEndDate=$_POST['auctionEndDate'];

$link=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, 'comp0022');
$query="Insert into ITEMS (email, itemTitle, description) VALUES
('$sellerEmail', '$auctionTitle', '$auctionDetails' )";
// $query="Insert into AUCTIONS (email, categoryID,startingPrice,currentPrice,reservePrice,endDate) VALUES
// ('$email', '$account_type', '$password' )";
if (mysqli_query($link, $query)){
    echo '<h2 class="my-3 text-center">Congratulations! Auction has been created</h2>';
    echo('<div class="text-center">Auction successfully created! <a href="mylistings.php">View your new listing.</a></div>');
} else{
    if (mysqli_errno($link)==1062){
        echo '<h2 class="my-3 text-center">Sorry! Auction has been created already</h2>';
        header("refresh:3;url=register.php");
        exit();
    }
    else {
        echo $link -> error;
    }
}


/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */


// If all is successful, let user know.



?>

</div>


<?php include_once("footer.php")?>
