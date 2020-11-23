<?php include_once("header.php")?>
<?php include_once("create_database.php");
?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Browse listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="browse.php">
  <div class="row">
    <div class="col-md-5 pr-0">
      <div class="form-group">
        <label for="keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="text" class="form-control border-left-0" name="keyword" placeholder="Search for anything">
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" name="cat">
          <option selected value="all">All categories</option>
          <?php
          $con = new mysqli("localhost", "root", "", "comp0022");
          // Check connection
          if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
          } 
          $sql = "select description from Category order by description ASC";
          $resource = mysqli_query($con,$sql);
          while($row = mysqli_fetch_assoc($resource)) {
            echo "<option value ='".$row['description']."'>".$row['description']."</option>";;
          }
          mysqli_close($con)
          ?>
        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" name="order_by">
          <option selected value="pricelow">Price (low to high)</option>
          <option value="pricehigh">Price (high to low)</option>
          <option value="date">Soonest expiry</option>
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>

<?php
  // Retrieve these from the URL
  if (!isset($_GET['keyword'])) {
    $keyword = "";
  }
  else {
    $keyword = $_GET['keyword'];
  }

  if (!isset($_GET['cat'])) {
    $sql_category = " ";
  }
  else {
    $category = $_GET['cat'];
    if ($category == "all"){
      $sql_category = " ";
    }
    else{
      $sql_category = " and categoryID = (select categoryID from category where description = '$category')";
    }
  }
  
  if (!isset($_GET['order_by'])) {
    $sql_ordering = " order by currentPrice ASC";
  }
  else {
    $ordering = $_GET['order_by'];
    if ($ordering == "pricelow"){
      $sql_ordering = " order by currentPrice ASC";
    }
    elseif ($ordering == "pricehigh"){
      $sql_ordering = " order by currentPrice DESC";
    }
    elseif ($ordering == "date"){
      $sql_ordering = " order by endDate ASC";
    }
  }
  
  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }

  $con = new mysqli("localhost", "root", "", "comp0022");
  // Check connection
  if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
  } 

  $sql_select = "SELECT Auction.itemID,title,description,currentPrice,endDate,num.num_bids
                  FROM Auction 
                  LEFT JOIN Item ON Auction.itemID = Item.itemID AND Auction.sellerEmail = Item.sellerEmail 
                  LEFT JOIN (SELECT BiddingHistory.itemID, COUNT(*) AS num_bids 
                        FROM BiddingHistory 
                        GROUP BY BiddingHistory.itemID) AS num
                  ON num.itemID = Auction.itemID
                  WHERE (title like '%$keyword%' or description like '%$keyword%') AND endDate > NOW()";
  $result1 = mysqli_query($con,$sql_select.$sql_category.$sql_ordering); 
  $num_results = mysqli_num_rows($result1);
  $results_per_page = 10;
  $max_page = ceil($num_results / $results_per_page);
  $min_limit = ($curr_page - 1) * $results_per_page;
  $max_limit = $curr_page * $results_per_page;
  $sql_limit = " limit $min_limit, $max_limit";
  mysqli_close($con);
?>

<div class="container mt-5">

<?php
  $con = new mysqli("localhost", "root", "", "comp0022");
  // Check connection
  if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
  } 
  $result2 = mysqli_query($con,$sql_select.$sql_category.$sql_ordering.$sql_limit);

  if (mysqli_num_rows($result2) == 0){
    echo('
    <h2>
      Don\'t get any results.
    </h2>
  ' );
  }
  else{
    echo '<ul class="list-group">';
    while($row = mysqli_fetch_assoc($result2)){
      $num_bids = $row["num_bids"];
      if($num_bids === NULL){
        $num_bids = 0;
      }
      $end_date = new DateTime($row["endDate"]);
      print_listing_li($row["itemID"], $row["title"], $row["description"], $row["currentPrice"], $num_bids, $end_date);
    }
    echo '</ul>';
  }
  mysqli_close($con);
?>



</ul>

<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">
  
<?php

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }
  
  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);
  
  if ($curr_page != 1) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }
    
  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('
    <li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('
    <li class="page-item">');
    }
    
    // Do this in any case
    echo('
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
  
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
?>

  </ul>
</nav>


</div>



<?php include_once("footer.php")?>