<?php
require_once __DIR__ . '/IPResolver.php';
require_once __DIR__ . '/provider/IpApiProvider.php';

class GeoLocator {

    public static function locateVoter(): array
    {
        $ip = IPResolver::getClientIP();

        // Localhost / dev handling
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return [
                'ip' => $ip,
                'country' => 'Localhost',
                'country_code' => 'LOCAL'
            ];
        }

        $location = IpApiProvider::locate($ip);

        return [
            'ip' => $ip,
            'country' => $location['country'] ?? 'Unknown',
            'country_code' => $location['country_code'] ?? 'UN'
        ];
    }
}
