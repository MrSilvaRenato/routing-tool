<?php
include 'db.php';
header('Content-Type: application/json');

if (isset($_FILES['spreadsheet'])) {
    require 'vendor/autoload.php';  // Load necessary libraries

    // Load the spreadsheet
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['spreadsheet']['tmp_name']);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();

    // Connect to the database

    // Initialize an array to collect errors
    $uploadErrors = [];

    foreach ($data as $row) {
        // Data from spreadsheet
        $number = $row[0];
        $street_name = $row[1];
        $suburb = $row[2];
        $city = $row[3];
        $postcode = $row[4];
        $phone = $row[5];
        $order_id = $row[6];
        $delivery_id = $row[7];

        // Get coordinates using the getCoordinates function
        $address = "{$number} {$street_name}, {$suburb}, {$city}, {$postcode}";
        $coordinates = getCoordinates($address);

        // Assign coordinates
        $latitude = $coordinates['lat'];
        $longitude = $coordinates['lng'];

        // Check if coordinates are valid (not 0, 0)
        if ($latitude == 0 && $longitude == 0) {
            $uploadErrors[] = [
                'address' => $address,
                'reason' => 'Invalid coordinates.'
            ];
            error_log("Skipping entry for $address: Invalid coordinates.");
            continue; // Skip this entry
        }

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO deliveries 
            (street_number, street_name, suburb, city, postcode, phone_number, order_id, delivery_id, latitude, longitude) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        if ($stmt) {
            $stmt->bind_param("isssssssdd", $number, $street_name, $suburb, $city, $postcode, $phone, $order_id, $delivery_id, $latitude, $longitude);
            if (!$stmt->execute()) {
                $uploadErrors[] = [
                    'address' => $address,
                    'reason' => 'Database query failed: ' . $stmt->error
                ];
                error_log("SQL Error for delivery_id $delivery_id: " . $stmt->error); // Log the SQL error
                $stmt->close();
                continue; // Skip to the next entry
            }
            $stmt->close();
        } else {
            $uploadErrors[] = [
                'address' => $address,
                'reason' => 'Database query preparation failed: ' . $conn->error
            ];
            error_log("Database query preparation failed: " . $conn->error); // Log preparation error
            continue; // Skip to the next entry
        }
    }

    // Close the connection
    $conn->close();

    // Prepare the response
    $response = ['message' => 'Spreadsheet uploaded successfully!'];
    if (!empty($uploadErrors)) {
        $response['errors'] = $uploadErrors; // Add errors to response if any
    }

    // Return the response as JSON
    echo json_encode($response);
    exit;
}

function getCoordinates($address) {
    $address = urlencode($address);
    $url = "https://nominatim.openstreetmap.org/search?q={$address}&format=json&addressdetails=1&limit=1";

    // Define options to set User-Agent header for the API request
    $options = [
        "http" => [
            "header" => "User-Agent: YourAppName/1.0\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    // If response is valid
    if ($response) {
        $json = json_decode($response, true);
        if (!empty($json) && isset($json[0]['lat']) && isset($json[0]['lon'])) {
            return [
                'lat' => (float)$json[0]['lat'],
                'lng' => (float)$json[0]['lon']
            ];
        }
    }

    // Return default coordinates if API fails or no result
    return ['lat' => 0, 'lng' => 0];
}
