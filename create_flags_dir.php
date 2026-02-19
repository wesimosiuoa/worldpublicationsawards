<?php
// Create flags directory
$flagsDir = 'assets/flags';

if (!file_exists($flagsDir)) {
    if (mkdir($flagsDir, 0755, true)) {
        echo "Flags directory created successfully at: $flagsDir<br>";
    } else {
        echo "Failed to create flags directory<br>";
    }
} else {
    echo "Flags directory already exists<br>";
}

// Create a sample flag file to test
$sampleFlag = $flagsDir . '/us.png';
if (!file_exists($sampleFlag)) {
    // Create a simple placeholder image
    $im = imagecreate(24, 24);
    $white = imagecolorallocate($im, 255, 255, 255);
    $blue = imagecolorallocate($im, 0, 0, 255);
    $red = imagecolorallocate($im, 255, 0, 0);
    
    // Simple US flag pattern
    imagefilledrectangle($im, 0, 0, 23, 23, $white);
    imagefilledrectangle($im, 0, 0, 11, 11, $blue);
    imageline($im, 0, 4, 23, 4, $red);
    imageline($im, 0, 8, 23, 8, $red);
    imageline($im, 0, 12, 23, 12, $red);
    imageline($im, 0, 16, 23, 16, $red);
    imageline($im, 0, 20, 23, 20, $red);
    
    imagepng($im, $sampleFlag);
    imagedestroy($im);
    echo "Sample US flag created<br>";
} else {
    echo "Sample flag already exists<br>";
}
?>