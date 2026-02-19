<?php
// Download country flags from FlagCDN to local assets/flags directory

set_time_limit(300); // 5 minutes timeout

// Create flags directory if it doesn't exist
$flagsDir = 'assets/flags';
if (!file_exists($flagsDir)) {
    mkdir($flagsDir, 0755, true);
    echo "Created flags directory: $flagsDir<br>";
}

// Common country ISO codes (you can expand this list)
$countries = [
    'us', 'gb', 'ca', 'au', 'de', 'fr', 'jp', 'cn', 'in', 'br', 
    'za', 'ru', 'mx', 'es', 'it', 'nl', 'se', 'no', 'dk', 'fi',
    'pl', 'cz', 'hu', 'ro', 'bg', 'hr', 'rs', 'ba', 'mk', 'al',
    'gr', 'tr', 'il', 'sa', 'ae', 'eg', 'ng', 'ke', 'gh', 'tz',
    'ug', 'zm', 'zw', 'mw', 'sz', 'ls', 'na', 'bw', 'za', 'mu',
    'sc', 'mg', 're', 'yt', 'km', 'so', 'dj', 'er', 'et', 'sd'
];

$downloaded = 0;
$failed = 0;

echo "<h3>Downloading Country Flags...</h3>";
echo "<p>Total countries to download: " . count($countries) . "</p>";

foreach ($countries as $iso) {
    $localPath = $flagsDir . '/' . $iso . '.png';
    $remoteUrl = 'https://flagcdn.com/w24/' . $iso . '.png';
    
    // Skip if already exists
    if (file_exists($localPath)) {
        echo "<span style='color: orange;'>✓ $iso.png (already exists)</span><br>";
        $downloaded++;
        continue;
    }
    
    // Download the flag
    $imageData = @file_get_contents($remoteUrl);
    
    if ($imageData !== false) {
        if (file_put_contents($localPath, $imageData)) {
            echo "<span style='color: green;'>✓ $iso.png downloaded successfully</span><br>";
            $downloaded++;
        } else {
            echo "<span style='color: red;'>✗ Failed to save $iso.png</span><br>";
            $failed++;
        }
    } else {
        echo "<span style='color: red;'>✗ Failed to download $iso.png from CDN</span><br>";
        $failed++;
    }
    
    // Small delay to be respectful to the server
    usleep(100000); // 0.1 second
}

echo "<hr>";
echo "<h4>Download Summary:</h4>";
echo "<p style='color: green;'><strong>Successful:</strong> $downloaded flags</p>";
echo "<p style='color: red;'><strong>Failed:</strong> $failed flags</p>";

if ($failed > 0) {
    echo "<p><strong>Note:</strong> Some flags may not be available from the CDN source.</p>";
    echo "<p>You can manually download missing flags from <a href='https://flagpedia.net/download' target='_blank'>Flagpedia.net</a></p>";
}

echo "<p><a href='index.php'>← Back to Homepage</a></p>";
?>