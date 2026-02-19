<?php
require_once __DIR__ . '/includes/dbcon.inc.php';
require_once __DIR__ . '/includes/fn.inc.php';

$stmt = $pdo->query("SELECT id, name FROM countries LIMIT 5");

foreach ($stmt as $row) {
    echo render_country_flag($row['id']) . ' ' . $row['name'] . '<br>';
}
