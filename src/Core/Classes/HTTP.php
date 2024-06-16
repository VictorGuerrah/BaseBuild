<?php

namespace App\Core\Classes;

class HTTP
{
    public static $_POST;

    public static function getStatusCodeMessage(int $status): string
    {
        $statusCodes = self::getStatusCodes();
        return $statusCodes[$status] ?? 'Unknown Status Code';
    }

    public static function sendResponse(int $httpStatusCode = 200, string $message = '', $contentType = 'text/html'): void
    {
        if (!headers_sent()) {
            self::setHeaders([
                "HTTP/1.1 $httpStatusCode not found.",
                "Content-type: $contentType"
            ]);
        }

        echo $message;
        exit;
    }

    public static function setPost(array $content): void
    {
        self::$_POST = $content;
    }

    public static function getStatusCodes(): array|string
    {
        $statusCodes = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            426 => 'Upgrade Required',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        ];

        return $statusCodes;
    }

    public static function setHeaders(array $headers): void
    {
        foreach ($headers as $header) {
            header($header);
        }
    }
}
