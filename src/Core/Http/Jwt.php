<?php

namespace App\Core\Http;

use App\Core\Environment;

class JWT
{
    private static array $header = ['alg' => 'HS256', 'type' => 'JWT'];
    private static string $iss = 'Basebuild';

    public static function create($content, int $keepLoggedInUntil): string
    {
        $base64Header = base64_encode(json_encode(self::$header));

        $payload = [];
        $payload['iat'] = time();
        $payload['iss'] = self::$iss;
        $payload['exp'] = $keepLoggedInUntil;
        $payload['data'] = $content;

        $base64Payload = base64_encode(json_encode($payload));

        $signatureHash = hash_hmac('sha256', $base64Header . '.' . $base64Payload, self::getKey(), true);
        $base64Signature = base64_encode($signatureHash);

        return "$base64Header.$base64Payload.$base64Signature";
    }


    public static function read(string $token): array
    {
        if (empty($token)) {
            throw new \Exception("Invalid token.", 401);
        }

        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new \Exception("Invalid token.", 401);
        }

        $base64Header = $parts[0];
        $base64Payload = $parts[1];
        $signature = $parts[2];

        $signatureHash = hash_hmac('sha256', $base64Header . '.' . $base64Payload, self::getKey(), true);
        $base64Signature = base64_encode($signatureHash);

        if (!hash_equals($signature, $base64Signature)) {
            throw new \Exception("Invalid token", 401);
        }


        $jsonPayload = base64_decode($base64Payload, true);
        if ($jsonPayload === false) {
            throw new \Exception("Invalid token", 401);
        }

        $payload = json_decode($jsonPayload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid token", 401);
        }

        if (time() > $payload['exp']) {
            throw new \Exception("Token has expired", 401);
        }

        if ($payload['iss'] != self::$iss) {
            throw new \Exception("Invalid issuer", 403);
        }

        return $payload;
    }

    private static function getKey(): string
    {
        return Environment::get('JWT_KEY');
    }
}
