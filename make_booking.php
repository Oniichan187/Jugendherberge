<?php
header("Content-Type: application/json");

try {
    $db = new SQLite3('jugendherberge_verwaltung.db');
} catch (Exception $e) {
    echo json_encode(["error" => "Fehler bei der Verbindung zur Datenbank"]);
    exit();
}

// Empfange die JSON-Daten von der Anfrage
$data = json_decode(file_get_contents('php://input'), true);

// Validierung: Überprüfe, ob die erforderlichen Daten vorhanden und gültig sind
if (!isset($data['zimmerID'], $data['checkinDate'], $data['checkoutDate'], $data['gastIDs']) || !is_array($data['gastIDs']) || empty($data['gastIDs'])) {
    echo json_encode(["error" => "Ungültige Daten - ZimmerID, Checkin/Checkout-Daten und mindestens eine GastID sind erforderlich"]);
    exit();
}

$zimmerID = $data['zimmerID'];
$checkinDate = $data['checkinDate'];
$checkoutDate = $data['checkoutDate'];
$gastIDs = $data['gastIDs'];

try {
    // Beginne eine Transaktion
    $db->exec('BEGIN');

    // Füge die Buchung in die Tabelle `buchungen` ein
    $stmt = $db->prepare("INSERT INTO buchungen (ZimmerID, CheckInDatum, CheckOutDatum) VALUES (:zimmerID, :checkinDate, :checkoutDate)");
    $stmt->bindValue(':zimmerID', $zimmerID, SQLITE3_INTEGER);
    $stmt->bindValue(':checkinDate', $checkinDate, SQLITE3_TEXT);
    $stmt->bindValue(':checkoutDate', $checkoutDate, SQLITE3_TEXT);
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception("Fehler beim Einfügen der Buchung in die Tabelle 'buchungen'");
    }

    // Hole die ID der neu eingefügten Buchung
    $buchungID = $db->lastInsertRowID();

    // Füge für jeden Gast in `gastIDs` einen Eintrag in `buchung_gaeste` ein
    $stmt = $db->prepare("INSERT INTO buchung_gaeste (BuchungID, GastID) VALUES (:buchungID, :gastID)");
    foreach ($gastIDs as $gastID) {
        $stmt->bindValue(':buchungID', $buchungID, SQLITE3_INTEGER);
        $stmt->bindValue(':gastID', $gastID, SQLITE3_INTEGER);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Fehler beim Einfügen in buchung_gaeste für GastID: $gastID");
        }
    }

    // Commit der Transaktion
    $db->exec('COMMIT');
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    // Rollback bei Fehler
    $db->exec('ROLLBACK');
    echo json_encode(["error" => $e->getMessage()]);
}
?>
