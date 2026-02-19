<?php
/***************************************
 * SIMPLE COUNTRY + FLAG TEST FILE
 * -------------------------------------
 * Updated to use helper functions
 ***************************************/

// Include the helper functions
require_once __DIR__ . '/includes/dbcon.inc.php';
require_once __DIR__ . '/includes/helpers.php';

// Get all countries with flags using the helper function
$countries = getAllCountriesWithFlags();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Country Flags Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 6px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .country {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .country:last-child {
            border-bottom: none;
        }
        .country-name {
            font-size: 16px;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Country Flags (CDN Test)</h1>

    <?php if (!empty($countries)): ?>
        <?php foreach ($countries as $country): ?>
            <div class="country">
                <?= renderFlag($country['iso_code']); ?>
                <span class="country-name">
                    <?= htmlspecialchars($country['name']); ?>
                </span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No countries found.</p>
    <?php endif; ?>
</div>

</body>
</html>
