<?php
header("Content-Type: application/json");

try {
    $db = new SQLite3('jugendherberge_verwaltung.db');
} catch (Exception $e) {
    echo json_encode(["error" => "Fehler bei der Verbindung zur Datenbank"]);
    exit();
}

// Abfrage, um alle Zimmer in der Datenbank zu erhalten
$query = "SELECT ZimmerID, JID, RoomNumber, BedCount, CategoryID, Availability FROM zimmer";
$result = $db->query($query);

$rooms = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $rooms[] = $row;
}

// Rückgabe der Zimmer als JSON
echo json_encode($rooms);
?>
