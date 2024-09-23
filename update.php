<?php
include 'db.php';
header('Content-Type: application/json');

// Handle Delete All request
if (isset($_POST['action']) && $_POST['action'] === 'delete_all') {
    $deleteQuery = "DELETE FROM deliveries";
    if ($conn->query($deleteQuery) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'All records deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting records: ' . $conn->error]);
    }
    $conn->close();
    exit; // Ensure no extra characters are sent
}

// Handle Delete Single Record request
if (isset($_POST['action']) && $_POST['action'] === 'delete_single' && isset($_POST['delivery_id'])) {
    $delivery_id = $_POST['delivery_id'];

    $deleteSingleStmt = $conn->prepare("DELETE FROM deliveries WHERE delivery_id = ?");
    $deleteSingleStmt->bind_param("s", $delivery_id); // 's' indicates a string type for delivery_id
    if ($deleteSingleStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Record deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $deleteSingleStmt->close();
    $conn->close();
    exit;
}

// Handle Drop Number Update request
if (isset($_POST['delivery_id']) && isset($_POST['drop_number'])) {
    $delivery_id = $_POST['delivery_id']; // Get the delivery_id from the request
    $drop_number = intval($_POST['drop_number']); // Ensure it's an integer

    // Log the incoming parameters for debugging
    error_log("Delivery ID: $delivery_id, Drop Number: $drop_number");

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
    exit; // Ensure no extra characters are sent
}

// If no valid action is received, return an error message
echo json_encode(['success' => false, 'error' => 'Invalid parameters or action.']);
$conn->close();

