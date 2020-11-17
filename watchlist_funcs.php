 <?php

if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}
include_once("connect_database.php");
// Extract arguments from the POST variables:
$item_id = $_POST['arguments'];
$username = $_SESSION['username'];

if ($_POST['functionname'] == "add_to_watchlist") {
  // TODO: Update database and return success/failure.
  $sql_insert_watchlist = "INSERT INTO WatchList(itemID, BuyerEmail) VALUES ($item_id,$username);";
  if ($conn->query($sql_insert_watchlist) === TRUE) {
    $res = "success";
  } else {
    $res = "failure";
  }
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.
  $sql_delete_watchlist = "DELETE FROM `WatchList` WHERE itemID = $item_id AND BuyerEmail = $username;";

  if ($conn->query($sql_delete_watchlist) === TRUE) {
    $res = "success";
  } else {
    $res = "failure";
  }
  
}
$conn->close();
// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;

?>