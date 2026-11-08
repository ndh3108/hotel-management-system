<?php

namespace App\Core;

class JWT {
    private static $secret_key = 'hotel_jwt_secret_key_2026'; // Change this in production
    private static $algorithm = 'HS256';

    /**
     * Encode payload to JWT
     * 
     * @param array $payload
     * @return string
     */
    public static function encode($payload) {
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ]);
        
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Decode JWT and return payload
     * 
     * @param string $jwt
     * @return mixed Array payload if valid, false if invalid
     */
    public static function decode($jwt) {
        $tokenParts = explode('.', $jwt);
        if(count($tokenParts) != 3) {
            return false;
        }
        
        $header = base64_decode(self::base64UrlDecode($tokenParts[0]));
        $payload = json_decode(base64_decode(self::base64UrlDecode($tokenParts[1])), true);
        $signatureProvided = $tokenParts[2];
        
        $signature = hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], self::$secret_key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        if ($base64UrlSignature === $signatureProvided) {
            // Optional: check expiration
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return false; // Token expired
            }
            return $payload;
        }
        
        return false;
    }

    private static function base64UrlEncode($data) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    private static function base64UrlDecode($data) {
        $b64 = str_replace(['-', '_'], ['+', '/'], $data);
        while (strlen($b64) % 4 != 0) {
            $b64 .= '=';
        }
        return $b64;
    }
}

