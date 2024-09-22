<?php
if (isset($_FILES['spreadsheet'])) {
    require 'vendor/autoload.php';  // Load necessary libraries

    // Load the spreadsheet
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['spreadsheet']['tmp_name']);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'perkii');
    if ($conn->connect_error) {
        die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
    }

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

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO deliveries 
            (street_number, street_name, suburb, city, postcode, phone_number, order_id, delivery_id, latitude, longitude) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        if ($stmt) {
            $stmt->bind_param("isssssssdd", $number, $street_name, $suburb, $city, $postcode, $phone, $order_id, $delivery_id, $latitude, $longitude);
            $stmt->execute();
            $stmt->close();
        } else {
            die(json_encode(['error' => 'Database query failed: ' . $conn->error]));
        }
    }

    // Close the connection
    $conn->close();

    // Return a success message
    echo json_encode(['message' => 'Spreadsheet uploaded successfully!']);
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
?>
