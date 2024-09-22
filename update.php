<?php
include 'db.php';
header('Content-Type: application/json');
if (isset($_POST['delivery_id']) && isset($_POST['drop_number'])) {
    $delivery_id = $_POST['delivery_id']; // Get the delivery_id from the request
    $drop_number = intval($_POST['drop_number']); // Ensure it's an integer

    // Log the incoming parameters for debugging
    error_log("Delivery ID: $delivery_id, Drop Number: $drop_number");

    // Database connection
 

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE deliveries SET drop_number = ? WHERE delivery_id = ?");
    $stmt->bind_param("is", $drop_number, $delivery_id); // 'i' for drop_number (integer), 's' for delivery_id (string)

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'delivery_id' => $delivery_id, 'drop_number' => $drop_number]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters.']);
}
?>
