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
    <div class="text-center">Your bid(<?php echo($bid)?>) is lower than current highest bid(<?php echo($highest_bid)?>).You will be redirected shortly.</div>

<?php else: ?>
    <div class="text-center">Congratulations! You are the top bidder currently! You will be redirected shortly.</div>
    <?php
    $now = date('Y-m-d H:i:s');
    echo($now);
    // Update the current price attribute of the auction
    $sql_update_auction="UPDATE Auction SET currentPrice=$bid WHERE itemID = $item_id";
    $conn->query($sql_update_auction);

    //send emails to other bidders to tell them they have been outbidden.
    $sql_select_item="SELECT title, `description` FROM Item WHERE itemID = $item_id;" ;
    $row_select_item = ($conn->query($sql_select_item))->fetch_assoc();
    $sql_select_bidding_history="SELECT buyerEmail FROM BiddingHistory WHERE itemID=$item_id GROUP BY buyerEmail";
    $result_select_bidding_history = $conn->query($sql_select_bidding_history);
    if ($result_select_bidding_history->num_rows > 0) {
        // send emails
        while($row_select_bidding_history = $result_select_bidding_history->fetch_assoc()) {
            if($row_select_bidding_history["buyerEmail"] != $_SESSION['username']){
                $to = $row_select_bidding_history["buyerEmail"];
                $subject = "You have been outbidden for ". $row_select_item["title"];
                $msg = "The current price of ".$row_select_item["title"]." is ". $bid .". ";

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
                    $mail->Subject = $subject;
                    $mail->Body    = $msg;
                    $mail->send();
                    //test code
                    //echo 'Message has been sent';
                } catch (Exception $e) {
                    //test code
                    //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
        }
    }

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
            $subject = "Someone just bidded on ". $row_select_item["title"];
            $msg = "The current price of ".$row_select_item["title"]." is ". $bid .". ";
            
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
                $mail->Subject = $subject;
                $mail->Body    = $msg;
                $mail->send();
                //test code
                //echo 'Message has been sent';
            } catch (Exception $e) {
                //test code
                //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }

    ?>
<?php endif ?>

<?php
//close the connection to database
$conn->close();
//redirect to original listing.php 
$url ="listing.php?item_id=".$item_id; 
header("refresh:5;url=$url")?>
