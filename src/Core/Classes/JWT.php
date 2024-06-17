<?php

namespace App\Core\Classes;

class JWT
{
    private static array $header = ['alg' => 'HS256', 'type' => 'JWT'];
    private static string $Iss = 'Basebuild';

    public static function create($content, int $keepLoggedInUntil): string
    {
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode(self::$header)));

        $payload = [];
        $payload['iat'] = time();
        $payload['iss'] = self::$Iss;
        $payload['expire'] = $keepLoggedInUntil;
        $payload['data'] = $content;
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

        $signatureHash = hash_hmac('sha256', $base64Header . $base64Payload, self::getKey(), true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signatureHash));


        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signatureHash));

        return "$base64Header . $base64Payload . $base64Signature";
    }

    private static function getKey(): string
    {
        return Environment::get('JWT_KEY');
    }
}
