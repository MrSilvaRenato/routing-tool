<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" /> <!-- Leaflet CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script> <!-- Popper.js -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> <!-- Bootstrap JS -->
    <style>
        #map {
            height: 500px;
            width: 100%;
            margin-top: 20px;
        }
    
        .popup-content {
            min-width: 200px; /* Set a minimum width for popups */
        }

        .popup-content strong {
            font-size: 1.2em;
        }

        .popup-content .form-group {
            margin-bottom: 1em; /* Space between form elements */
        }

        .popup-content button {
            margin-top: 10px; /* Space above buttons */
    }
</style>
    </style>
</head>
<body>
<div class="container">
    <!-- Controls container with Upload and Buttons -->
    <div id="controls" class="d-flex justify-content-center my-3">
        <div class="d-flex align-items-center">
            <!-- Upload form -->
            <form id="uploadForm" enctype="multipart/form-data" class="form-inline">
                <div class="custom-file mb-2 mr-2">
                    <input type="file" name="spreadsheet" accept=".xlsx, .xls, .csv" required class="custom-file-input" id="customFile">
                    <label class="custom-file-label" for="customFile">Upload Manifest</label>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Upload</button>
            </form>

            <!-- Buttons outside the form but still aligned -->
            <button type="button" id="deleteAllBtn" class="btn btn-danger mb-2 ml-2" onclick="deleteAllRecords()">Delete All</button>
            <button type="button" id="optimizeDropsBtn" class="btn btn-success mb-2 ml-2" onclick="optimizeDrops()">Optimize Drops</button>
        </div>
    </div>

    <!-- Map container with proper sizing -->
    <div id="map" class="w-100" style="height: 600px;"></div>
</div>
<footer><p class="text-center">Basic Routing Tool by Renato</p></footer>
    <script src="js.js"></script>
</body>
</html>
