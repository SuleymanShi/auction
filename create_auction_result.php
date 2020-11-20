<?php include_once("header.php")?>

<div class="container my-5">

<?php

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */
    // $_SESSION['username'] = "test";
    $sherhold = 100;
    $newCategoryID = 0;

    $con = @mysqli_connect('localhost','root','','comp0022',3306);     //connect to mysql
    if(mysqli_connect_errno()){
        exit(mysqli_connect_error());
    }
    mysqli_set_charset($con,'utf8');

    if($_POST['auctionEndDate']<date("Y-m-d H:i:s")){
      echo"<script>alert('Wrong endDate');history.go(-1);</script>";
    }

    if(strlen($_POST['auctionTitle'])==0 or strlen($_POST['auctionDetails'])==0){
        echo"<script>alert('Please input the title or details');history.go(-1);</script>";
    }

    if($_POST['auctionStartPrice']==0){
      echo"<script>alert('Please enter the price');history.go(-1);</script>";
    }

    if(strlen($_POST['auctionTitle'])>30){                           //check the validation of data
        echo"<script>alert('The length of Title overflow(longer than 30)');history.go(-1);</script>";
    }

    if(strlen($_POST['auctionDetails'])>200){
      echo"<script>alert('The length of Details overflow(longer than 200)');history.go(-1);</script>";
    }


    if($_POST['auctionCate']=='Choose...'){
      echo"<script>alert('Please select a certain category');history.go(-1);</script>";
    }

    $auctionCategory = $_POST['auctionCate'];

    if($_POST['auctionCate']=='other' and $_POST['newCategory']!=''){
      $newCategory = $_POST['newCategory'];
      $newcat = "select amount from newCategory where newdescription = \"".$newCategory."\"";
      $resource = mysqli_query($con,$newcat);
      if(mysqli_num_rows($resource)!=0){
        $addcount = "update newCategory SET amount = amount + 1 where newdescription = \"".$newCategory."\";";
        $add = mysqli_query($con, $addcount);
        if(!$add)
        {
        exit('Can\'t add the amount: '.mysqli_error($con));
        };
        $findID = "select newCategoryID from newCategory where newdescription = \"".$newCategory."\"";
        $resource = mysqli_query($con, $findID);
        if(!$resource)
        {
        exit('Can\'t find the ID: '.mysqli_error($con));
        };
        while($row = mysqli_fetch_assoc($resource)) {
          $newCategoryID = $row["newCategoryID"];
        }

      }else{$sql = "INSERT INTO newCategory ".
                  "(newdescription,amount) ".
                  "VALUES ".
                  "('$newCategory',1)";
              $ins = mysqli_query($con, $sql);
              if(!$ins)
              {
              exit('Can\'t insert new Category: '.mysqli_error($con));
              }
              $findID = "select newCategoryID from newCategory where newdescription = \"".$newCategory."\"";
              $resource = mysqli_query($con, $findID);
               if(!$resource)
               {
               exit('Can\'t find the ID: '.mysqli_error($con));
                };
               while($row = mysqli_fetch_assoc($resource)) {
                 $newCategoryID = $row["newCategoryID"];
               }
            }
          }

    $findnum = "select newdescription from newCategory where amount >= $sherhold";
    $resource = mysqli_query($con,$findnum);
    if(mysqli_num_rows($resource)!=0){
      while($row = mysqli_fetch_assoc($resource)) {
        $newdes = $row["newdescription"];
        $sql = "INSERT INTO Category ".
               "(description) ".
               "VALUES ".
              "('$newdes')";
         $ins = mysqli_query($con, $sql);
         if(!$ins)
         {
         exit('Can\'t insert new Category: '.mysqli_error($con));
         }

         $sql = "SELECT newcategoryID from newcategory where newdescription = \"".$newdes."\"";
         $sel = mysqli_query($con,$sql);
         if(!$sel)
         {
         exit('Can\'t find the newcateID: '.mysqli_error($con));
         }
         while($row = mysqli_fetch_assoc($sel)) {
          $oldID = $row["newcategoryID"];
        }
         $sql = "DELETE FROM newCategory where newdescription = \"".$newdes."\"";
         $del = mysqli_query($con, $sql);
         if(!$del)
         {
         exit('Can\'t delete new Category: '.mysqli_error($con));
         }
         $findID = "select categoryID from Category where description = \"".$newdes."\"";
         $sel = mysqli_query($con, $findID);
         if(!$sel)
         {
         exit('Can\'t find categoryID: '.mysqli_error($con));
         }
         while($row = mysqli_fetch_assoc($sel)) {
          $changeID = $row["categoryID"];
        }
         $auctionCategory = $_POST['newCategory'];
         $change = "update Auction SET CategoryID = $changeID where newcategoryID = \"".$oldID."\";";
          $cha = mysqli_query($con, $change);
         if(!$cha)
         {
          exit('Can\'t add the amount: '.mysqli_error($con));
         };
         $change = "update Auction SET newcategoryID = 0 where newcategoryID = \"".$oldID."\";";
          $cha = mysqli_query($con, $change);
         if(!$cha)
         {
          exit('Can\'t add the amount: '.mysqli_error($con));
         };
      }
    }

    $title = $_POST['auctionTitle'];                       //extract data from users and mysql

    $description = $_POST['auctionDetails'];

    $categoryID = 0;



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
            "(title,description,sellerEmail) ".
            "VALUES ".
            "('$title','$description','$username')";

    $ins = mysqli_query($con, $sql);
    if(!$ins)
    {
      exit('Can\'t save your data of item: '.mysqli_error($con));
    }

    $itemID = 0;

    $findID = "select max(itemID) from item";

    $resource = mysqli_query($con,$findID);
    while($row = mysqli_fetch_assoc($resource)) {
      $itemID = $row["max(itemID)"];
    }


    $sql = "INSERT INTO Auction ".                                //insert all the data into mysql
            "(itemID,categoryID,startingPrice,currentPrice,reservePrice,endDate,sellerEmail,newCategoryID ) ".
            "VALUES ".
            "('$itemID','$categoryID','$startingPrice','$currentPrice','$reservePrice','$endDate','$username','$newCategoryID')";

    $ins = mysqli_query($con, $sql);
    if(!$ins)
    {
      exit('Can\'t save your data of auction: '.mysqli_error($con));
    }

                   //inform users after saving all the data

/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'],
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */


/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */
    mysqli_close($con);


// If all is successful, let user know.
echo('<div class="text-center">Auction successfully created! <a href="mylistings.php">View your new listing.</a></div>');

?>

</div>


<?php include_once("footer.php")?>
