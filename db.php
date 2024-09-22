<?php
// db.php
$conn = new mysqli('localhost', 'root', '', 'perkii');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


<!-- var popupContent = `
                    <strong>Address: ${location.street_number} ${location.street_name}</strong><br>
                    Drop Number: <input type="number" id="dropNumber${location.delivery_id}" value="${location.drop_number || ''}" />
                    <button onclick="assignDrop(${location.delivery_id}, ${location.latitude}, ${location.longitude})">Assign Drop</button>
                `;
 -->