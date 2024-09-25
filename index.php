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

        /* Ensure custom file input works with Bootstrap */
        .custom-file-label::after {
            content: "Browse"; /* Display proper 'Browse' text */
        }

        /* Adjust the spacing for buttons */
        #deleteAllBtn, #optimizeDropsBtn {
            margin-left: 10px; /* Space between buttons */
        }
        .btn {
    height: 38px; /* Adjust this height to match */
}

@media (max-width: 768px) {
    #map {
        height: 400px; /* Reduce map height for mobile screens */
    }

    .d-flex.align-items-center {
        flex-direction: column; /* Stack the buttons vertically on smaller screens */
    }

    #deleteAllBtn, #optimizeDropsBtn {
        margin-left: 0; /* Remove the margin when stacked vertically */
        margin-top: 10px; /* Add some top margin */
    }
}

.google-maps-icon {
            position: relative;
            width: 100px;
            height: 100px;
            background-color: #4285F4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .google-maps-icon::before {
            position: absolute;
            width: 60px;
            height: 60px;
            background-color: white;
            border-radius: 50%;
        }
        .google-maps-icon::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            background-color: #34A853;
            border-radius: 50%;
            top: 35px;
        }
    </style>
</head>
<body>
<div class="container">
    <div>HARDICK BUM FACE</div>
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
                <button type="button" id="deleteAllBtn" class="btn btn-danger mb-2 ml-2" onclick="deleteAllRecords()">Delete All</button>
                <button type="button" id="optimizeDropsBtn" class="btn btn-success mb-2 ml-2" onclick="optimizeDrops()">Optimize Drops</button>
            </form>      
        </div>
    </div>

    <div id="loadingMessage" class="alert alert-info" style="display:none;">
            Manifest is getting uploaded, please wait<span id="loadingDots">...</span>
        </div>

        <div id="message" class="my-3"></div>
        <div id="errorMessages" style="color: red;"></div>
    <!-- Map container with proper sizing -->
    <div id="map" class="w-100" style="height: 600px;"></div>
</div>
<footer><p class="text-center">Basic Routing Tool by Renato</p></footer>
    <script src="js.js"></script>
</body>
</html>
