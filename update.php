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

// Check if the request is to delete all records
if (isset($_POST['action']) && $_POST['action'] === 'delete_all') {
    $deleteAllStmt = $conn->prepare("DELETE FROM deliveries");
    if ($deleteAllStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'All records deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $deleteAllStmt->close();
    $conn->close();
    exit;
}

// Check if the request is to delete a single record
if (isset($_POST['action']) && $_POST['action'] === 'delete_single' && isset($_POST['delivery_id'])) {
    $delivery_id = $_POST['delivery_id'];

    $deleteSingleStmt = $conn->prepare("DELETE FROM deliveries WHERE delivery_id = ?");
    $deleteSingleStmt->bind_param("s", $delivery_id);
    if ($deleteSingleStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Record deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $deleteSingleStmt->close();
    $conn->close();
    exit;
}

// Handle drop number update
if (isset($_POST['delivery_id']) && isset($_POST['drop_number'])) {
    $delivery_id = $_POST['delivery_id'];
    $drop_number = intval($_POST['drop_number']);

    $stmt = $conn->prepare("UPDATE deliveries SET drop_number = ? WHERE delivery_id = ?");
    $stmt->bind_param("is", $drop_number, $delivery_id);

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
