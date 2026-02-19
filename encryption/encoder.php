<?php

    function salted_encode(string $value): string
    {
        $secret_salt = "WPA2026_SECRET"; // change this

        // Add salt before value
        $combined = $secret_salt . $value;

        // Encode
        $base64 = base64_encode($combined);

        // Make URL-safe
        return rtrim(strtr($base64, '+/', '-_'), '=');
    }
?>