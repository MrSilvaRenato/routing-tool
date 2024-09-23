<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" /> <!-- Add Leaflet CSS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        /* Flexbox for aligning upload form and delete button side by side */
        #controls {
            display: flex;
            gap: 10px; /* Space between upload button and delete button */
            margin-bottom: 20px;
        }

        #map {
            height: 500px;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Upload Spreadsheet</h2>

    <!-- Flexbox container for upload and delete all controls -->
    <div id="controls">
        <form id="uploadForm" enctype="multipart/form-data" onsubmit="uploadSpreadsheet(event)">
            <input type="file" name="spreadsheet" accept=".xlsx, .xls, .csv" required>
            <input type="submit" value="Upload">
        </form>
        <button type="button" id="deleteAllBtn" onclick="deleteAllRecords()">Delete All</button>
    </div>

    <div id="message"></div>

    <h2>Delivery Map</h2>
    <div id="map"></div>

    <script src="js.js"></script>
</body>
</html>