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
    <div class="container mt-5">
        

    <div id="controls" class="d-flex justify-content-center my-3">
    <!-- Flex container for form and buttons -->
    <div class="d-flex align-items-center">
        <!-- Upload form -->
        <form id="uploadForm" enctype="multipart/form-data" class="form-inline">
            <div class="custom-file mb-2 mr-2">
                <input type="file" name="spreadsheet" accept=".xlsx, .xls, .csv" required class="custom-file-input" id="customFile">
                <label class="custom-file-label" for="customFile">Upload Manifest</label>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Upload</button>
        </form>
           
        </div>

        <div id="loadingMessage" class="alert alert-info" style="display:none;">
            Manifest is getting uploaded, please wait<span id="loadingDots">...</span>
        </div>

        <div id="message" class="my-3"></div>

        <h2 class="text-center">Delivery Map</h2>
        <div id="map"></div>
    </div>
<footer><p class="text-center">Basic Routing Tool by Renato</p></footer>
    <script src="js.js"></script>
</body>
</html>
