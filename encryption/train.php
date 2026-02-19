<?php 

    
    require_once 'decoder.php';

    $name = isset($_GET['name']) ? $_GET['name'] : null;
    if ($name) {
        $decoded_name = salted_decode($name);
        echo "Decoded Name: " . ($decoded_name !== null ? $decoded_name : "Invalid or tampered data") . "\n";
    } else {
        echo "No name provided in the query string.\n";
    }
?>