<?php
$db = new SQLite3('jugendherberge_verwaltung.db');

$result = $db->query("SELECT JID, Name FROM jugendherbergen");
$hostels = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $hostels[] = $row;
}

echo json_encode($hostels);
?>
