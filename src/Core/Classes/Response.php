<?php

namespace App\Core\Classes;

use App\Core\Classes\HTTP;
use App\Core\Classes\View;

class Response
{
    protected string $status;
    protected string $statusCode;
    protected array $data = [];
    protected string $message = '';

    public function message(string $message): Response 
    {
        $this->message = $message;
        return $this;
    }

    public function sendJson(array|string $data = [], int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $body = json_encode($this->getData());

        HTTP::setHeaders([
            200,
            "Content-type: application/json"
        ]);

        $this->output($body);
    }

    private function getData()
    {
        return [
            'status' => HTTP::getStatusCodeMessage($this->statusCode),
            'statusCode' => $this->statusCode,
            'data' => $this->data,
            'message' => $this->message
        ];
    }

    public function sendView(string $path, array $data = []): void
    {
        $body = View::load($path, $data);
        HTTP::setHeaders([
            200,
            "Content-type: application/json"
        ]);
        $this->output($body);
    }

    private function output(string $body): void
    {
        if (ob_get_contents() && ob_get_length()) {
            @ob_end_clean();
        }

        print $body;
        exit;
    }

    public function sendHandleError()
    {
        $body = View::load('exception-screen');
        HTTP::setHeaders([
            500,
            "Content-type: text/html"
        ]);
        $this->output($body);
    }
}