<?php include_once("header.php")?>

<div class="container my-5">

<?php

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */
    $con = @mysqli_connect('localhost','root','','comp0022',3306);     //connect to mysql
    if(mysqli_connect_errno()){
        exit(mysqli_connect_error());
    }
    mysqli_set_charset($con,'utf8');

    if(strlen($_POST['auctionTitle'])>30){                           //check the validation of data
        echo"<script>alert('The length of Title overflow(longer than 30)');history.go(-1);</script>";
    }

    if(strlen($_POST['auctionDetails'])>200){
      echo"<script>alert('The length of Details overflow(longer than 200)');history.go(-1);</script>";
    }


    if($_POST['auctionCate']=='Choose...'){
      echo"<script>alert('Please select a certain category');history.go(-1);</script>";
    }

    $itemID = 0;                                          //get the itemID from mysql

    $find = "select * from auction";
    if(!mysqli_query($con,$find)){
        $itemID=1;
    }else{
        $max = "select max(itemID) from auction";
        $resource = mysqli_query($con,$max);
        $maxID = mysqli_fetch_assoc($resource);
        $itemID = $maxID['max(itemID)'] + 1;
    }

    $title = $_POST['auctionTitle'];                       //extract data from users and mysql

    $description = $_POST['auctionDetails'];

    $categoryID = 0;

    $auctionCategory = $_POST['auctionCate'];

    $cat = "select categoryID from Category where description = \"".$auctionCategory."\"";
    $resource = mysqli_query($con,$cat);
    while($row = mysqli_fetch_assoc($resource)) {
      $categoryID = $row["categoryID"];
    }


    $startingPrice = (double)$_POST['auctionStartPrice'];

    $currentPrice = $startingPrice;

    $reservePrice = (double)$_POST['auctionReservePrice'];

    $endDate = $_POST['auctionEndDate'];

    $username = $_SESSION['username'];

    $sql = "INSERT INTO Item ".
            "(itemID,title,description,sellerEmail) ".
            "VALUES ".
            "('$itemID','$title','$description','$username')";

    $ins = mysqli_query($con, $sql);
    if(!$ins)
    {
      exit('Can\'t save your data of item: '.mysqli_error($con));
    }

    $sql = "INSERT INTO Auction ".                                //insert all the data into mysql
            "(itemID,categoryID,startingPrice,currentPrice,reservePrice,endDate,sellerEmail ) ".
            "VALUES ".
            "('$itemID','$categoryID','$startingPrice','$currentPrice','$reservePrice','$endDate','$username')";

    $ins = mysqli_query($con, $sql);
    if(!$ins)
    {
      exit('Can\'t save your data of auction: '.mysqli_error($con));
    }



    echo "Successfully save your data<br />";                        //inform users after saving all the data

/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'],
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */


/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */
    mysqli_close($con);


// If all is successful, let user know.
echo('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');



?>

</div>


<?php include_once("footer.php")?>
