<?php
    //crontab code:
    // 0 0 * * * /Applications/MAMP/bin/php/php7.4.2/bin/php  /Applications/MAMP/htdocs/acution_func_1/send_email_daily.php

?>
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
            $all_msg = file_get_contents("/var/www/auction/msg.html");
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

// Send emails to winners

$yesterday_date = ((new DateTime())->modify("-1 day")->format('Y-m-d'));
$sql_select_winner_email_list = "SELECT EndItem.itemID, Item.sellerEmail, Item.title, Item.description, BiddingHistory.buyerEmail, Auction.currentPrice AS finalPrice, Auction.reservePrice
                            FROM (SELECT itemID FROM Auction WHERE endDate like '$yesterday_date%') AS EndItem
                            LEFT JOIN Item ON Item.itemID = EndItem.itemID
                            LEFT JOIN Auction ON Auction.itemID = EndItem.itemID
                            LEFT JOIN BiddingHistory ON (Auction.itemID = BiddingHistory.itemID AND Auction.currentPrice = BiddingHistory.bidPrice)
                            ;";

include_once("connect_database.php");
$result_select_winner_email_list = $conn->query($sql_select_winner_email_list);
if ($result_select_winner_email_list->num_rows > 0) {
    // send emails
    while($row_select_winner_email_list = $result_select_winner_email_list->fetch_assoc()) {
        $auction_succeed = ($row_select_winner_email_list["reservePrice"] != 0 && $row_select_winner_email_list["buyerEmail"] != NULL &&  $row_select_winner_email_list["finalPrice"] >=$row_select_winner_email_list["reservePrice"])
                            || ($row_select_winner_email_list["reservePrice"] == 0 && $row_select_winner_email_list["buyerEmail"] != NULL);
        $itemID_end = $row_select_winner_email_list["itemID"];
        $sql_notice_watchList = "SELECT buyerEmail FROM WatchList WHERE itemID = $itemID_end;";
        $result_notice_watchList = $conn->query($sql_notice_watchList);
        
        if($auction_succeed){
            // auction has been sold

            // send email to buyer
            $to = $row_select_winner_email_list["buyerEmail"];
            $subject = "[Auction] Congratulations! You have won the auction!";
            $msg = "You have won the ".$row_select_winner_email_list["title"]."! Your final bid was &pound".number_format($row_select_winner_email_list["finalPrice"],2).".";        
            send_mail($to, $subject, $msg);

            //send email to seller
            $to = $row_select_winner_email_list["sellerEmail"];
            $subject = "[Auction] Congratulations! Your auction has been sold!";
            $msg = "You have sold the ".$row_select_winner_email_list["title"]."! The final bid was &pound".number_format($row_select_winner_email_list["finalPrice"],2).".";        
            send_mail($to, $subject, $msg);

            //send email to watchlist
            if ($result_notice_watchList->num_rows > 0) {
                // send emails
                while($row_notice_watchList = $result_notice_watchList->fetch_assoc()) {
                    $to = $row_notice_watchList["buyerEmail"];
                    $subject = "[Auction] The auction on your watchlist has ended.";
                    $msg = "The auction of ".$row_select_winner_email_list["title"]." has ended. The final price is &pound".number_format($row_select_winner_email_list["finalPrice"],2).".";
                    send_mail($to, $subject, $msg);
                }
            }
        }else{
            //auction has been passed in

            if($row_select_winner_email_list["buyerEmail"] == NULL){
                // there is no bidder on this auction
                $subject = "[Auction] Sorry, Your auction has been passed in.";
                $msg = "Your aution(".$row_select_winner_email_list["title"].") has been passed in.";
                $to = $row_select_winner_email_list["sellerEmail"];
                send_mail($to, $subject, $msg);
                //send email to watchlist
                if ($result_notice_watchList->num_rows > 0) {
                    // send emails
                    while($row_notice_watchList = $result_notice_watchList->fetch_assoc()) {
                        $to = $row_notice_watchList["buyerEmail"];
                        $subject = "[Auction] The auction on your watchlist has ended.";
                        $msg = "The auction of ".$row_select_winner_email_list["title"]." has been passed in";
                        send_mail($to, $subject, $msg);
                    }
                }
            }else{
                // fail to reach the reserve price

                // send email to seller
                $subject = "[Auction] Sorry, Your auction has been passed.";
                $msg = "Your aution(".$row_select_winner_email_list["title"].") has been passed in.";
                $to = $row_select_winner_email_list["sellerEmail"];
                $final_price = $row_select_winner_email_list['finalPrice'];
                $msg .= (" The final bid is &pound".number_format($final_price,2).".");
                send_mail($to, $subject, $msg);

                // send email to buyer
                $subject = "[Auction] Sorry, Your didn't win the auction.";
                $msg = "Your bid(&pound".number_format($row_select_winner_email_list["finalPrice"],2).") didn't reach the reserve price.";
                $to = $row_select_winner_email_list["buyerEmail"];
                send_mail($to, $subject, $msg);

                //send email to watchlist
                if ($result_notice_watchList->num_rows > 0) {
                    // send emails
                    while($row_notice_watchList = $result_notice_watchList->fetch_assoc()) {
                        $to = $row_notice_watchList["buyerEmail"];
                        $subject = "[Auction] The auction on your watchlist has ended.";
                        $msg = "The auction of ".$row_select_winner_email_list["title"]." has been passed in because the final price (&pound".number_format($row_select_winner_email_list["finalPrice"],2).") failed to reach the reserve price.";
                        send_mail($to, $subject, $msg);
                    }
                }
            }
            
        }
    }
}
$conn->close();
?>
