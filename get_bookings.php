<?php
header("Content-Type: application/json");

try {
    $db = new SQLite3('jugendherberge_verwaltung.db');
} catch (Exception $e) {
    echo json_encode(["error" => "Fehler bei der Verbindung zur Datenbank"]);
    exit();
}

$query = "
    SELECT g.Name AS GastName, g.Email AS GastEmail, j.Name AS Jugendherberge, 
           z.RoomNumber, b.CheckInDatum, b.CheckOutDatum
    FROM buchungen b
    JOIN buchung_gaeste bg ON b.BuchungID = bg.BuchungID
    JOIN gaeste g ON bg.GastID = g.GastID
    JOIN zimmer z ON b.ZimmerID = z.ZimmerID
    JOIN jugendherbergen j ON z.JID = j.JID
";

$result = $db->query($query);

$bookings = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $bookings[] = $row;
}

echo json_encode($bookings);
?>
