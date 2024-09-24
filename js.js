// Initialize the map once
var map = L.map('map').setView([-27.4698, 153.0251], 8);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

document.addEventListener('DOMContentLoaded', function() {
    loadMapMarkers();

    // Prevent default form submission and handle file upload
    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent page reload
        uploadSpreadsheet(); // Call without passing event
    });

    setInterval(loadMapMarkers, 30000);
});

// Function to handle spreadsheet upload
function uploadSpreadsheet() {
    const formData = new FormData(document.getElementById('uploadForm')); // Get the form data

    // Show loading message
    const loadingMessage = document.getElementById('loadingMessage');
    loadingMessage.style.display = 'block';
    animateLoadingDots();

    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        const messageDiv = document.getElementById('message');
        loadingMessage.style.display = 'none'; // Hide loading message

        if (result.message) {
            messageDiv.textContent = result.message;
            messageDiv.style.color = 'green';
            loadMapMarkers(); // Refresh markers after upload
        } else if (result.error) {
            messageDiv.textContent = 'Error: ' + result.error;
            messageDiv.style.color = 'red';
        }
    })
    .catch(err => {
        console.error('Error uploading spreadsheet:', err);
        const messageDiv = document.getElementById('message');
        loadingMessage.style.display = 'none'; // Hide loading message
        messageDiv.textContent = 'Error uploading spreadsheet.';
        messageDiv.style.color = 'red';
    });
}

// Function to animate loading dots
function animateLoadingDots() {
    const loadingDots = document.getElementById('loadingDots');
    let dotCount = 0;
    setInterval(() => {
        dotCount = (dotCount + 1) % 4; // Cycle through 0 to 3
        loadingDots.textContent = '.'.repeat(dotCount); // Update dots
    }, 500); // Change dots every 500ms
}

function loadMapMarkers() {
    fetch('get_deliveries.php') // Fetch deliveries from the server
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(locations => {
            if (!Array.isArray(locations)) {
                throw new Error('Expected an array of locations');
            }

            // Clear existing markers before adding new ones
            map.eachLayer(function(layer) {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });

            // Add markers to the map
            locations.forEach(location => {
                if (location.latitude && location.longitude) {
                    var marker = L.marker([location.latitude, location.longitude]).addTo(map);

                    // Popup content with drop number input field, assign drop button, and delete record button
                    var popupContent = `
                    <div class="popup-content">
                        <strong>Address:</strong><br>
                        <span>${location.street_number} ${location.street_name}</span><br>
                        <span>${location.suburb}, ${location.city}</span><br><br>
                        
                        <div class="form-group">
                            <label for="dropNumber${location.delivery_id}">Drop Number:</label>
                            <input type="number" class="form-control" id="dropNumber${location.delivery_id}" value="${location.drop_number || ''}" />
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-success" onclick="assignDrop('${location.delivery_id}')">Assign Drop</button>
                            <button class="btn btn-danger" onclick="deleteRecord('${location.delivery_id}')">Delete Record</button>
                        </div>
                    </div>
                `;
                    marker.bindPopup(popupContent).openPopup();

                    // Show drop number on the marker if assigned
                    if (location.drop_number) {
                        var dropLabel = `${location.drop_number}`;
                        var dropIcon = L.divIcon({
                            className: 'drop-icon',
                            html: `<div style="color: red; font-size: 20px; font-weight: bold; text-align: center;">
                            ${dropLabel}
                        </div>`,
                            iconSize: [30, 42],
                            popupAnchor: [0, -30]
                        });
                        marker.setIcon(dropIcon);
                    }
                } else {
                    console.error('Location missing coordinates:', location);
                }
            });
        })
        .catch(err => console.error('Error fetching locations:', err));
}


// Function to assign a drop number
function assignDrop(deliveryId) {
    var dropNumber = document.getElementById(`dropNumber${deliveryId}`).value; // Ensure this references the correct drop number

    console.log('Assigning drop for delivery ID:', deliveryId);
    if (dropNumber) {
        var formData = new FormData();
        formData.append('delivery_id', deliveryId); // Ensure this uses the correct delivery ID
        formData.append('drop_number', dropNumber);

        // Log the data being sent
        console.log('Sending data:', {
            delivery_id: deliveryId,
            drop_number: dropNumber
        });

        fetch('update.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Ensure response is parsed as JSON
        .then(result => {
            // Log the result from the server
            console.log('Received response:', result);
            const messageDiv = document.getElementById('message');
            if (result.success) {
                messageDiv.textContent = 'Drop number assigned successfully!';
                messageDiv.style.color = 'green'; // Success message color
                loadMapMarkers(); // Reload markers after assigning the drop number
            } else {
                messageDiv.textContent = 'Error assigning drop number: ' + result.error;
                messageDiv.style.color = 'red'; // Error message color
            }
        })
        .catch(err => console.error('Error assigning drop number:', err));
    } else {
        alert('Please enter a valid drop number.');
    }
}

// Function to delete all records
function deleteAllRecords() {
    if (confirm('Are you sure you want to delete all records? This action cannot be undone.')) {
        fetch('update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=delete_all'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('All records deleted successfully.');
                loadMapMarkers(); // Refresh markers
            } else {
                alert('Error deleting records: ' + result.error);
            }
        })
        .catch(err => console.error('Error deleting records:', err));
    }
}

// Function to delete individual record
function deleteRecord(deliveryId) {
    if (confirm('Are you sure you want to delete this record?')) {
        fetch('update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_single&delivery_id=${deliveryId}`
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Record deleted successfully.');
                loadMapMarkers(); // Refresh markers
            } else {
                alert('Error deleting record: ' + result.error);
            }
        })
        .catch(err => console.error('Error deleting record:', err));
    }
}

// Update the file input label on file selection
document.getElementById('customFile').addEventListener('change', function(event) {
    var label = event.target.nextElementSibling;
    label.textContent = event.target.files[0] ? event.target.files[0].name : 'Choose file';
});



function optimizeDrops() {
    var origin = { lat: -27.4664, lng: 153.0881 }; // 470 Lytton Road coordinates

    fetch('get_deliveries.php')
        .then(response => response.json())
        .then(locations => {
            if (locations && locations.length > 0) {
                // Calculate distances from the origin
                locations.forEach(location => {
                    var distance = getDistance(origin, {
                        lat: parseFloat(location.latitude),
                        lng: parseFloat(location.longitude)
                    });
                    location.distance = distance; // Add distance property to each location
                });

                // Sort by distance (closest first)
                locations.sort((a, b) => a.distance - b.distance);

                // Automatically assign drop numbers based on sorted locations
                locations.forEach((location, index) => {
                    var dropNumber = index + 1; // Drop 1, Drop 2, etc.
                    console.log(`Optimizing drop ${dropNumber} for delivery ID: ${location.delivery_id}`);

                    // Send the drop number to the server
                    var formData = new FormData();
                    formData.append('delivery_id', location.delivery_id);
                    formData.append('drop_number', dropNumber);

                    fetch('update.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            console.log(`Drop ${dropNumber} assigned to delivery ID: ${location.delivery_id}`);
                            loadMapMarkers(); // Reload markers after optimizing drops
                        } else {
                            console.error(`Error optimizing drop for delivery ID: ${location.delivery_id}`);
                        }
                    })
                    .catch(err => console.error(`Error optimizing drop for delivery ID: ${location.delivery_id}`, err));
                });
            } else {
                console.error('No deliveries found to optimize.');
            }
        })
        .catch(error => console.error('Error optimizing drops:', error));
}

// Helper function to calculate distance between two coordinates
function getDistance(origin, destination) {
    var lat1 = origin.lat;
    var lon1 = origin.lng;
    var lat2 = destination.lat;
    var lon2 = destination.lng;

    var R = 6371e3; // metres
    var φ1 = lat1 * Math.PI/180; // φ, λ in radians
    var φ2 = lat2 * Math.PI/180;
    var Δφ = (lat2 - lat1) * Math.PI/180;
    var Δλ = (lon2 - lon1) * Math.PI/180;

    var a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ/2) * Math.sin(Δλ/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    var distance = R * c; // in metres
    return distance;
}
