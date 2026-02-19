<?php
class IPResolver {

    public static function getClientIP(): string
    {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ipList = explode(',', $_SERVER[$key]);
                return trim($ipList[0]); // first IP = client
            }
        }

        return '0.0.0.0';
    }
}
