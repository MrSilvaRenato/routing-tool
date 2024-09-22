<?php
$cleardb_url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$db_host = $cleardb_url["host"];
$db_user = $cleardb_url["user"];
$db_pass = $cleardb_url["pass"];
$db_name = substr($cleardb_url["path"], 1); // Remove the leading '/'

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>