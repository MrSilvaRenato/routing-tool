<?php
include 'db.php';
header('Content-Type: application/json');

// Query to select necessary fields including coordinates and current drop numbers
$result = $conn->query("
    SELECT 
        delivery_id, 
        street_number, 
        street_name, 
        suburb, 
        city, 
        latitude, 
        longitude, 
        drop_number 
    FROM deliveries
    WHERE latitude IS NOT NULL AND longitude IS NOT NULL
");

$locations = [];

// Fetch each row and add it to the $locations array
while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}

// Encode the result as JSON and output it
echo json_encode($locations);

// Close the database connection
$conn->close();
?>