<?php
/**
 * Test script to verify the new flag helper functions
 */

// Include the database connection and helper functions
require_once __DIR__ . '/includes/dbcon.inc.php';
require_once __DIR__ . '/includes/helpers.php';

echo "<h2>Testing Flag Helper Functions</h2>";

// Test getCountryNameByISO function
echo "<h3>Test: getCountryNameByISO()</h3>";
echo "US: " . getCountryNameByISO('US') . "<br>";
echo "GB: " . getCountryNameByISO('GB') . "<br>";
echo "FR: " . getCountryNameByISO('FR') . "<br>";
echo "Invalid code: " . getCountryNameByISO('XX') . "<br>";

// Test getCountryFlag function with different sizes
echo "<h3>Test: getCountryFlag() with various sizes</h3>";
echo "US flag (24px): " . getCountryFlag('US', 24) . " United States<br>";
echo "GB flag (32px): " . getCountryFlag('GB', 32) . " United Kingdom<br>";
echo "DE flag (48px): " . getCountryFlag('DE', 48) . " Germany<br>";

// Test renderFlag function (from simple_flag_test.php functionality)
echo "<h3>Test: renderFlag() function</h3>";
echo "CA flag (40px): " . renderFlag('CA') . " Canada<br>";
echo "JP flag (60px): " . renderFlag('JP', 60) . " Japan<br>";

// Test getAllCountriesWithFlags function
echo "<h3>Test: getAllCountriesWithFlags() - First 10 countries</h3>";
$countries = getAllCountriesWithFlags();
if (!empty($countries)) {
    echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";
    $count = 0;
    foreach ($countries as $country) {
        if ($count >= 10) break; // Show only first 10 for this test
        echo "<div style='display: flex; align-items: center; margin: 5px; padding: 5px; border: 1px solid #ddd;'>";
        echo renderFlag($country['iso_code'], 24);
        echo $country['name'];
        echo "</div>";
        $count++;
    }
    echo "</div>";
} else {
    echo "No countries found in database.";
}

// Test getFlagEmoji function
echo "<h3>Test: getFlagEmoji() function</h3>";
echo "US: " . getFlagEmoji('US') . " United States<br>";
echo "GB: " . getFlagEmoji('GB') . " United Kingdom<br>";
echo "FR: " . getFlagEmoji('FR') . " France<br>";
?>