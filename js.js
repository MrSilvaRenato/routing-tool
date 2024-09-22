// Initialize the map once
var map = L.map('map').setView([-27.4698, 153.0251], 8); // Brisbane coordinates
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Load the map markers once the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    loadMapMarkers();  // Load markers on page load

    // Refresh the markers every 30 seconds (adjust the time as needed)
    setInterval(loadMapMarkers, 30000); // 30000ms = 30 seconds
});

function loadMapMarkers() {
    fetch('get_deliveries.php') // Fetch deliveries from the server
        .then(response => response.json())
        .then(locations => {
            // Clear existing markers before adding new ones
            map.eachLayer(function(layer) {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });

            // Add markers to the map
            locations.forEach(location => {
                var marker = L.marker([location.latitude, location.longitude]).addTo(map);

                // Popup content with drop number input field
                var popupContent = `
                    <strong>Address: ${location.street_number} ${location.street_name}<br>${location.suburb}</strong><br>
                    <input type="number" id="dropNumber${location.delivery_id}" value="${location.drop_number || ''}" />
                    <button onclick="assignDrop('${location.delivery_id}')">Assign Drop</button> <!-- Use quotes for delivery_id -->
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
