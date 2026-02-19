<?php

    function salted_decode(string $value): ?string
    {
        $secret_salt = "WPA2026_SECRET"; // must match encoder

        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        if ($decoded === false) {
            return null;
        }

        // Check salt
        if (strpos($decoded, $secret_salt) !== 0) {
            return null; // tampered or invalid
        }

        // Remove salt
        return substr($decoded, strlen($secret_salt));
    }
?>