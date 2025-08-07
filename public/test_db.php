<?php

$servername = "localhost"; // Or "127.0.0.1", or the specific hostname if provided
$port = 3314;             // Your specific port
$username = "myapp_user"; // Your DB_USERNAME
$password = "zverZVER1."; // Your DB_PASSWORD
$dbname = "myapp_db";     // Your DB_DATABASE

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to database!";
$conn->close();

?>