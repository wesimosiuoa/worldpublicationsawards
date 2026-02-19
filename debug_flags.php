<?php
// Debug file to check flag CDN accessibility

include 'includes/dbcon.inc.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Flag Debug Test</title>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-item { margin: 10px 0; padding: 10px; border: 1px solid #ddd; }
        .success { background-color: #d4edda; }
        .error { background-color: #f8d7da; }
        img { vertical-align: middle; margin-right: 6px; }
    </style>
</head>
<body>
    <h1>Flag Debug Test</h1>";

// Test raw CDN URLs directly
$testCodes = ['US', 'us', 'GB', 'gb', 'CA', 'ca', 'ZA', 'za'];

echo "<h2>Testing Raw CDN URLs</h2>";
foreach ($testCodes as $code) {
    $url = "https://flagcdn.com/w24/$code.png";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<div class='test-item " . ($httpCode == 200 ? 'success' : 'error') . "'>";
    echo "Code: $code | URL: $url | HTTP: $httpCode ";
    if ($httpCode == 200) {
        echo "| <img src='$url' width='24' height='24'> SUCCESS";
    } else {
        echo "| FAILED";
    }
    echo "</div>";
}

// Test with file_get_contents as alternative
echo "<h2>Testing with file_get_contents</h2>";
foreach (['us', 'gb', 'ca'] as $code) {
    $url = "https://flagcdn.com/w24/$code.png";
    $imageExists = @file_get_contents($url) !== false;
    
    echo "<div class='test-item " . ($imageExists ? 'success' : 'error') . "'>";
    echo "Code: $code | Exists: " . ($imageExists ? 'YES' : 'NO');
    if ($imageExists) {
        echo " | <img src='$url' width='24' height='24'> WORKS";
    } else {
        echo " | FAILED";
    }
    echo "</div>";
}

// Check database countries with more debugging
echo "<h2>Countries in Database</h2>";
try {
    $stmt = $pdo->query("SELECT id, name, iso_code FROM countries ORDER BY name");
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($countries as $country) {
        $id = $country['id'];
        $name = htmlspecialchars($country['name']);
        $iso_code = $country['iso_code']; // Keep original case
        
        echo "<div class='test-item'>";
        echo "ID: $id | Name: $name | ISO: '$iso_code'";
        
        // Test different variations
        $variations = [$iso_code, strtolower($iso_code), strtoupper($iso_code)];
        $found = false;
        
        foreach ($variations as $variant) {
            if (@file_get_contents("https://flagcdn.com/w24/$variant.png") !== false) {
                echo " | <img src='https://flagcdn.com/w24/$variant.png' width='24' height='24'> Found with '$variant'";
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            echo " | ❌ No flag found for any variation";
        }
        
        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<div class='error'>Database error: " . $e->getMessage() . "</div>";
}

echo "
    <h2>Raw Image Tags (Direct HTML)</h2>
    <p>US: <img src='https://flagcdn.com/w24/us.png' width='24' height='24'> USA</p>
    <p>UK: <img src='https://flagcdn.com/w24/gb.png' width='24' height='24'> UK</p>
    <p>ZA: <img src='https://flagcdn.com/w24/za.png' width='24' height='24'> South Africa</p>
    
    <p><a href='test_flags.php'>← Back to Test Flags</a> | <a href='index.php'>Home</a></p>
</body>
</html>";
?>