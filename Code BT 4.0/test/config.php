<?php
$servername = "localhost";
$username = "root";
$password = "Terrel21";
$dbname = "bander_travel";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
