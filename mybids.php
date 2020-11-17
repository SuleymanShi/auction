<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My bids</h2>

<?php
  // This page is for showing a user the auctions they've bid on.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up the auctions they've bidded on.
  
  // TODO: Loop through results and print them out as list items.
  
?>

<?php
  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }
?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->

<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing
     retrieved from the query -->

<?php
  
  // Demonstration of what listings will look like using dummy data.
  $item_id = "87021";
  $title = "Dummy title";
  $description = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget rutrum ipsum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus feugiat, ipsum vel egestas elementum, sem mi vestibulum eros, et facilisis dui nisi eget metus. In non elit felis. Ut lacus sem, pulvinar ultricies pretium sed, viverra ac sapien. Vivamus condimentum aliquam rutrum. Phasellus iaculis faucibus pellentesque. Sed sem urna, maximus vitae cursus id, malesuada nec lectus. Vestibulum scelerisque vulputate elit ut laoreet. Praesent vitae orci sed metus varius posuere sagittis non mi.";
  $current_price = 30;
  $num_bids = 1;
  $end_date = new DateTime('2020-09-16T11:00:00');
  
  // This uses a function defined in utilities.php
  print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
  
  $item_id = "516";
  $title = "Different title";
  $description = "Very short description.";
  $current_price = 13.50;
  $num_bids = 3;
  $end_date = new DateTime('2020-11-02T00:00:00');
  
  print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);

  // TODO: Check user's credentials (cookie/session).
  if (!isset($_SESSION['account_type'])  || $_SESSION['logged_in'] != true || $_SESSION['account_type'] != 'buyer') {
    echo("You have not logged in or are not a buyer. You will be redirected shortly.");
    header('refresh:5;url=browse.php');
  }

  // TODO: Perform a query to pull up the auctions they've bidded on.
  
  //connect to database.
  include_once("connect_database.php");
  //for test
  $_SESSION['username'] = '871222456@qq.com';
  $username = $_SESSION['username'];

  $sql_number_of_bided_items =
  "SELECT COUNT(itemId) AS number_of_bided_items
  FROM BiddingHistory
  WHERE BiddingHistory.buyerEmail = '$username' 
  GROUP BY BiddingHistory.itemID;";
  $result = $conn->query($sql_number_of_bided_items);
  $row = $result->fetch_assoc();
 
  
  /* For the purposes of pagination, it would also be helpful to know the
  total number of results that satisfy the above query */

  $num_results = $row["number_of_bided_items"]; // TODO: Calculate me for real
  $results_per_page = 1;
  $max_page = ceil($num_results / $results_per_page);


  $offset = ($curr_page-1) * $results_per_page;
  $sql_auction_bidded_on =
  "SELECT BiddingHistory.itemID, Item.title, Item.description, Auction.currentPrice,
  Auction.endDate, num.num_bids 
  FROM BiddingHistory 
  LEFT JOIN Auction ON BiddingHistory.itemID = Auction.itemID 
  LEFT JOIN Item ON BiddingHistory.itemID = Item.itemID 
  LEFT JOIN (SELECT BiddingHistory.itemID, COUNT(*) AS num_bids 
              FROM BiddingHistory 
              GROUP BY BiddingHistory.itemID) AS num
            ON num.itemID = BiddingHistory.itemID
  WHERE BiddingHistory.buyerEmail = '$username' 
  GROUP BY BiddingHistory.itemID 
  LIMIT $offset, $results_per_page;";

  $result = $conn->query($sql_auction_bidded_on);

  // TODO: Loop through results and print them out as list items.
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      print_listing_li($row["itemID"], $row["title"], $row["description"], $row["currentPrice"], $row["num_bids"],$row["endDate"]);
    }
  } else {
    echo "You haven't bidded on anything.";
  }
  // close the connection to database.
  $conn->close();
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
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
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
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
  
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
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