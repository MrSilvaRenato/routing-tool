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

.map-pin {
  width: 30px; /* Width of the pin */
  height: 40px; /* Height of the pin */
  background-color: red; /* Main color of the pin */
  position: relative;
  border-radius: 15px 15px 0 0; /* Rounded top */
  margin: 0 auto; /* Center the pin */
}

.map-pin::after {
  content: '';
  position: absolute;
  bottom: -15px; /* Positioning the bottom part */
  left: 50%;
  margin-left: -10px; /* Center the bottom part */
  width: 20px; /* Width of the bottom point */
  height: 20px; /* Height of the bottom point */
  background-color: red; /* Same color as the top part */
  border-radius: 50%; /* Round bottom */
  transform: translateY(0) rotate(45deg); /* Sharp bottom point */
}

.map-pin:before {
  content: '';
  position: absolute;
  top: 10px; /* Adjust this to position the inner circle */
  left: 50%;
  margin-left: -10px; /* Center the inner circle */
  width: 20px; /* Width of the inner circle */
  height: 20px; /* Height of the inner circle */
  background-color: white; /* Inner circle color */
  border-radius: 50%; /* Make it round */
  z-index: 1; /* Bring the inner circle above the pin */
}
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
