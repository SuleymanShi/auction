<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  if (!isset($_SESSION['account_type']) || $_SESSION['logged_in'] != true || $_SESSION['account_type'] != 'buyer' ) {
    header('refresh:5;url=browse.php');
    exit("You have not logged in or are not a buyer. You will be redirected shortly.");
   }
  // TODO: Perform a query to pull up auctions they might be interested in.
  include_once("connect_database.php");
  $username = $_SESSION['username'];
  $now_time = ((new DateTime())->format('Y-m-d H:i:s'));
  $sql_auction_interested =  "SELECT BiddingHistory.itemID, Item.title, Item.description, Auction.currentPrice,
                          Auction.endDate, num.num_bids 
                          FROM BiddingHistory 
                          LEFT JOIN Auction ON BiddingHistory.itemID = Auction.itemID 
                          LEFT JOIN Item ON BiddingHistory.itemID = Item.itemID 
                          LEFT JOIN (SELECT BiddingHistory.itemID, COUNT(*) AS num_bids 
                                      FROM BiddingHistory 
                                      GROUP BY BiddingHistory.itemID) AS num
                                    ON num.itemID = BiddingHistory.itemID
                          LEFT JOIN (SELECT ItemID, COUNT(*) AS similar_bids 
                                      FROM (SELECT DISTINCT buyerEmail,ItemID
                                            FROM BiddingHistory 
                                            WHERE buyerEmail IN (SELECT buyerEmail 
                                                                  FROM BiddingHistory 
                                                                  WHERE (ItemID IN (SELECT ItemID 
                                                                                    FROM BiddingHistory 
                                                                                    WHERE buyerEmail = '$username' 
                                                                                    GROUP BY ItemID
                                                                                    )
                                                                        )
                                                                )
                                            ) AS Temp
                                      GROUP BY ItemID
                                    ) AS num2 ON num2.ItemID = BiddingHistory.ItemID
                          WHERE (BiddingHistory.buyerEmail IN (SELECT buyerEmail From BiddingHistory WHERE itemID IN 
                                                                      (SELECT itemID FROM BiddingHistory 
                                                                      WHERE buyerEmail = '$username'
                                                                      GROUP BY itemID
                                                                      )
                                                                GROUP BY buyerEmail
                                                              )
                                )
                                AND 
                                (BiddingHistory.itemID NOT IN (SELECT itemID FROM BiddingHistory WHERE BuyerEmail = '$username'
                                                                GROUP BY itemID
                                                              )
                                )
                                AND
                                Auction.endDate > NOW()
                          GROUP BY BiddingHistory.itemID
                          ORDER BY num2.similar_bids DESC;
                          ";
  $result_auction_interested = $conn->query($sql_auction_interested);
  //test code
  //echo($username);
  
  // TODO: Loop through results and print them out as list items.
  if ($result_auction_interested->num_rows > 0) {
    // output data of each row
    while($row = $result_auction_interested->fetch_assoc()) {
      $end_date = new DateTime($row["endDate"]);
      print_listing_li($row["itemID"], $row["title"], $row["description"], $row["currentPrice"], $row["num_bids"],$end_date);
    }
  } else {
    echo "Sorry, no recommendation founded.";
  }

  $conn->close();
?>