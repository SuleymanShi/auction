<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once("PHPMailer/PHPMailer.php");
require_once("PHPMailer/SMTP.php");
require_once("PHPMailer/Exception.php");
require_once("PHPMailer/OAuth.php");
require_once("PHPMailer/POP3.php");
?>

<?php
function send_mail($to, $subject, $msg){
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'comp0022.noreply@gmail.com';                     // SMTP username
            $mail->Password   = 'comp0022';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            //Recipients
            $mail->setFrom('comp0022.noreply@gmail.com', 'comp0022.noreply');
            $mail->addAddress($to);     // Add a recipient
            // Content
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $all_msg = file_get_contents("msg.html");
            $all_msg = str_replace("msg",$msg,$all_msg);
            $mail->AltBody = $msg;
            $mail->Subject = $subject;
            $mail->Body    = $all_msg;
            $mail->send();
            //test code
            //echo 'Message has been sent';
        } catch (Exception $e) {
            //test code
            //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
}
?>

<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to make a bid.
// Notify user of success/failure and redirect/give navigation options.


session_start();
$item_id = $_SESSION["item_id"];
$buyerEmail = $_SESSION["username"];
$bid = $_POST["bid"];
include_once("connect_database.php");
$sql_highest_bid = "SELECT currentPrice FROM Auction Where itemID = $item_id";
$result_highest_bid = $conn->query($sql_highest_bid);
$row_highest_bid = $result_highest_bid->fetch_assoc();
$highest_bid = $row_highest_bid["currentPrice"];

?>

<?php if ($bid <= $highest_bid): ?>
    <div style="text-align: center;font-size:30px"><b>bid(<?php echo($bid)?>) is lower than current highest bid(<?php echo($highest_bid)?>).You will be redirected shortly.</b></div>

<?php else: ?>
    <div style="text-align: center;font-size:30px"><b>Congratulations! You are the top bidder of this item currently! <b></div>
    <div style="text-align: center;font-size:30px"><b>We will send your a confirmation email.  <b></div>
    <div style="text-align: center;font-size:30px"><b>You will be redirected shortly or you can click the link below. <b></div>
    <div style="text-align: center;font-size:30px"> <a href=<?php echo("listing.php?item_id=".$item_id); ?>>Back</a></div>
    
    <?php
    $now = date('Y-m-d H:i:s');
    // Update the current price attribute of the auction
    $sql_update_auction="UPDATE Auction SET currentPrice=$bid WHERE itemID = $item_id";
    $conn->query($sql_update_auction);

    //send emails to last top-bidders to tell him/her that he/she has been outbid.
    $sql_select_item="SELECT title, `description` FROM Item WHERE itemID = $item_id;" ;
    $row_select_item = ($conn->query($sql_select_item))->fetch_assoc();
    $sql_select_bidding_history="SELECT buyerEmail FROM BiddingHistory WHERE itemID=$item_id AND biddingTime = (SELECT MAX(biddingTime) FROM BiddingHistory WHERE itemID = $item_id);";
    $result_select_bidding_history = $conn->query($sql_select_bidding_history);
    $row_select_bidding_history = $result_select_bidding_history->fetch_assoc();
    if ($result_select_bidding_history->num_rows > 0) {
        $to = $row_select_bidding_history["buyerEmail"];
        $subject = "[Auction] You have been outbid...";
        $msg = "Sorry, you have been outbid for ". $row_select_item["title"] ."."."The current price of ".$row_select_item["title"]." is &pound". number_format($bid,2) .". ";
        send_mail($to, $subject, $msg);
    }

    //send confirmation email to current bidder
    send_mail($buyerEmail,"[Auction] This is a confirmation email","Congratulations! You are the top bidder of ".$row_select_item["title"]." currently! Your bid is "."&pound".number_format($bid).".");

    // Update the bidding history
    $sql_insert_bidding_history="INSERT INTO BiddingHistory(itemID, buyerEmail, biddingTime, bidPrice) VALUES ($item_id,'$buyerEmail','$now',$bid);";
    $conn->query($sql_insert_bidding_history);

    // Send emails to watchlist users
    $sql_select_watchlist = "SELECT buyerEmail FROM WatchList WHERE itemID = $item_id;";
    $result_select_watchlist = $conn->query($sql_select_watchlist);
    if ($result_select_watchlist->num_rows > 0) {
        // send emails
        while($row_select_watchlist = $result_select_watchlist->fetch_assoc()) {
            $to = $row_select_watchlist["buyerEmail"];
            $subject = "[Auction] Someone just bid on ". $row_select_item["title"];
            $msg = "Someone just bid on ". $row_select_item["title"].". The current price of ".$row_select_item["title"]." is &pound". number_format($bid,2) .". ";
            send_mail($to, $subject, $msg);
        }
    }

    ?>
<?php endif ?>

<?php
//close the connection to database
$conn->close();
//redirect to original listing.php 
$url ="listing.php?item_id=".$item_id; 
header("refresh:15;url=$url")?>
