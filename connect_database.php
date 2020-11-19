<?php
$connect_database_servername = "localhost";
$connect_database_username = "root";
$connect_database_password = "";
$connect_database_dbname = "comp0022";

// Create connection
$conn = new mysqli($connect_database_servername, $connect_database_username, $connect_database_password, $connect_database_dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>