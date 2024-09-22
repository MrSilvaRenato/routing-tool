<?php
include 'db.php';
header('Content-Type: application/json');
// Make sure to select the drop_number as well
$result = $conn->query("SELECT delivery_id, street_number, street_name, suburb, latitude, longitude, drop_number FROM deliveries");
$locations = [];

while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}

echo json_encode($locations);
$conn->close();
?>
