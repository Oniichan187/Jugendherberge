<?php
$db = new SQLite3('jugendherberge_verwaltung.db');

$result = $db->query("SELECT Name, Email FROM gaeste");
$guests = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $guests[] = $row;
}

echo json_encode($guests);
?>
