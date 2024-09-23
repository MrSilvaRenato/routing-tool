<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Map</title>
  
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" /> <!-- Add Leaflet CSS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
    <h2>Upload Spreadsheet</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
        <input type="file" name="spreadsheet" accept=".xlsx, .xls, .csv" required>
        <input type="submit" value="Upload">
    </form>
    <button id="deleteAllBtn">Delete All
    <div id="message"></div>
    <h2>Delivery Map</h2>
    <div id="map" style="height: 500px; width: 100%;"></div>
    
    <script src="js.js"></script>  <!-- External JS file -->
</body>
</html>
