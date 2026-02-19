<?php
class IpApiProvider {

    public static function locate(string $ip): ?array
    {
        $url = "http://ip-api.com/json/{$ip}?fields=status,country,countryCode";

        $response = @file_get_contents($url);
        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);

        if ($data['status'] !== 'success') {
            return null;
        }

        return [
            'country' => $data['country'],
            'country_code' => $data['countryCode']
        ];
    }
}
