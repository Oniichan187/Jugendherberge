document.addEventListener("DOMContentLoaded", function() {
    loadExistingUsers();
    loadHostels();
    document.getElementById("checkinDate").addEventListener("change", loadAvailableRooms);
    document.getElementById("checkoutDate").addEventListener("change", loadAvailableRooms);
    document.getElementById("bookButton").addEventListener("click", makeBooking);
    loadBookings();
});

let allGuests = []; // Globale Variable zum Speichern aller Gäste

// Funktion zum Laden aller Gäste
function loadExistingUsers() {
    fetch('get_guests.php')
        .then(response => response.json())
        .then(data => {
            allGuests = data; // Speichert alle Gäste global
            let userList = document.getElementById("existingUsers");
            userList.innerHTML = data.map(user => `${user.GastID} ${user.Name} (${user.Email})`).join("<br>");
        });
}

function loadHostels() {
    fetch('get_hostels.php')
        .then(response => response.json())
        .then(data => {
            let hostelDropdown = document.getElementById("hostelDropdown");
            hostelDropdown.innerHTML = data.map(h => `<option value="${h.JID}">${h.Name}</option>`).join("");
            loadAvailableRooms();
        });
}

function loadAvailableRooms() {
    // Lese die ausgewählte Jugendherbergs-ID (JID) aus
    let selectedJID = document.getElementById("hostelDropdown").value;

    fetch('get_rooms.php')
        .then(response => response.json())
        .then(rooms => {
            // Filtere die Zimmer, die verfügbar sind und zur ausgewählten Jugendherberge gehören
            const availableRooms = rooms.filter(room => room.Availability === "Available" && room.JID == selectedJID);

            // Dropdown mit verfügbaren Zimmern füllen
            const roomDropdown = document.getElementById("roomDropdown");
            if (availableRooms.length === 0) {
                roomDropdown.innerHTML = `<option>Keine freien Zimmer gefunden</option>`;
            } else {
                roomDropdown.innerHTML = availableRooms.map(room =>
                    `<option value="${room.ZimmerID}">${room.RoomNumber} (${room.BedCount} Betten)</option>`
                ).join("");
            }
        })
        .catch(error => {
            document.getElementById("roomDropdown").innerHTML = `<option>Fehler bei der Verbindung: ${error.message}</option>`;
        });
}

// Buchung durchführen
function makeBooking() {
    const zimmerID = document.getElementById("roomDropdown").value;
    const checkinDate = document.getElementById("checkinDate").value;
    const checkoutDate = document.getElementById("checkoutDate").value;
    const gastInput = document.getElementById("bookingGuests").value;

    // Extrahiere Gast-IDs aus der Eingabe
    const gastIDs = gastInput.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));
    
    // Überprüfen, ob gastIDs leer ist
    if (gastIDs.length === 0) {
        alert("Keine gültigen Gäste-IDs für die Buchung ausgewählt.");
        return;
    }

    fetch('make_booking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            zimmerID: zimmerID,
            checkinDate: checkinDate,
            checkoutDate: checkoutDate,
            gastIDs: gastIDs
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert("Buchung erfolgreich!");
            loadBookings(); // Funktion zum Neuladen der Buchungstabelle
        } else {
            alert("Fehler bei der Buchung: " + result.error);
        }
    })
    .catch(error => {
        alert("Verbindungsfehler: " + error.message);
    });
}

function loadBookings() {
    fetch('get_bookings.php')
        .then(response => response.json())
        .then(bookings => {
            const bookingTableBody = document.getElementById("bookingTableBody");
            if (bookings.length === 0) {
                bookingTableBody.innerHTML = `<tr><td colspan="5">Keine Buchungen gefunden</td></tr>`;
            } else {
                bookingTableBody.innerHTML = bookings.map(booking => `
                    <tr>
                        <td>${booking.GastName}</td>
                        <td>${booking.Jugendherberge}</td>
                        <td>${booking.RoomNumber}</td>
                        <td>${booking.CheckInDatum}</td>
                        <td>${booking.CheckOutDatum}</td>
                    </tr>
                `).join("");
            }
        })
        .catch(error => {
            console.error("Fehler beim Laden der Buchungen:", error);
        });
}
