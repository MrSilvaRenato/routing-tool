<?php
$conn = new mysqli('localhost', 'root', '', 'perkii');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Make sure to select the drop_number as well
$result = $conn->query("SELECT delivery_id, street_number, street_name, suburb, latitude, longitude, drop_number FROM deliveries");
$locations = [];

while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}

echo json_encode($locations);
$conn->close();
?>
