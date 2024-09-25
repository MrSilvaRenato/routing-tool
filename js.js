var map = L.map('map').setView([-27.455431, 153.084387], 13);  // Replace with your initial view setup

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
}).addTo(map);

let markers = [];  // Array to store your markers
let selectedMarkers = [];  // Array to store selected markers
let selectionBox;
let startPoint, endPoint;  // For selection box coordinates

// Load map markers function
function loadMapMarkers() {
    // Fetch deliveries from server
    fetch('get_deliveries.php')
        .then(response => response.json())
        .then(deliveries => {
            deliveries.forEach(delivery => {
                // Create a marker and add to map
                let marker = L.marker([delivery.lat, delivery.lng]).addTo(map);

                // Add marker to markers array
                markers.push(marker);

                // Popup content for the marker
                marker.bindPopup(`
                    <b>Delivery:</b> ${delivery.address}<br>
                    <b>Drop Number:</b> ${delivery.drop_number}
                    <br>
                    <input type="text" placeholder="Enter Drop Number" id="drop_${delivery.id}">
                    <button onclick="assignDrop(${delivery.id})">Assign Drop</button>
                    <button onclick="deleteRecord(${delivery.id})">Delete</button>
                `);
            });
        })
        .catch(error => console.error('Error loading markers:', error));
}

// Right-click to start selection box
map.on('mousedown', function (e) {
    if (e.originalEvent.button === 2) {  // Right-click
        startPoint = e.latlng;  // Record starting point of the box
        selectionBox = L.rectangle([startPoint, startPoint], { color: 'blue', weight: 1 }).addTo(map);
        map.on('mousemove', resizeSelectionBox);  // Track the mouse movement to resize the box
    }
});

// Resize the selection box as the mouse moves
function resizeSelectionBox(e) {
    endPoint = e.latlng;
    selectionBox.setBounds([startPoint, endPoint]);
}

// Right-click release to select markers within the box
map.on('mouseup', function (e) {
    if (e.originalEvent.button === 2 && selectionBox) {  // Right-click release
        map.off('mousemove', resizeSelectionBox);  // Stop resizing the box

        // Get bounds of the selection box
        let bounds = selectionBox.getBounds();

        // Highlight markers within the selection box
        selectedMarkers = markers.filter(marker => bounds.contains(marker.getLatLng()));

        // Highlight selected markers
        selectedMarkers.forEach(marker => {
            marker.setIcon(new L.Icon({
                iconUrl: 'marker.png',  // Path to the image in the root
                iconSize: [25, 41],     // Adjust size if needed
            }));
        });

        // Remove the selection box after selection
        map.removeLayer(selectionBox);
        selectionBox = null;
    }
});

// Function to assign drop number to the marker (You already had this)
function assignDrop(id) {
    let dropNumber = document.getElementById(`drop_${id}`).value;
    fetch('update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&drop_number=${dropNumber}`
    })
    .then(response => response.text())
    .then(result => {
        console.log(result);  // Handle success message, or display it
        alert('Drop number assigned successfully');
        loadMapMarkers();  // Reload markers after assignment
    })
    .catch(error => console.error('Error assigning drop number:', error));
}

// Function to delete a record (You already had this)
function deleteRecord(id) {
    fetch('delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}`
    })
    .then(response => response.text())
    .then(result => {
        console.log(result);  // Handle success message, or display it
        alert('Record deleted successfully');
        loadMapMarkers();  // Reload markers after deletion
    })
    .catch(error => console.error('Error deleting record:', error));
}

// Load the markers when the map is ready
loadMapMarkers();


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





