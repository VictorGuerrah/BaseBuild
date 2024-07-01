<?php

namespace App\Core\Http;

use App\Core\Http\Http;
use App\Core\View\View;

class Response
{
    protected string $status;
    protected string $statusCode;
    protected array|string $data = [];
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

        Http::setHeaders([
            200,
            "Content-type: application/json"
        ]);

        $this->output($body);
    }

    private function getData()
    {
        return [
            'status' => Http::getStatusCodeMessage($this->statusCode),
            'statusCode' => $this->statusCode,
            'data' => $this->data,
            'message' => $this->message
        ];
    }

    public function sendView(string $path, array $data = []): void
    {
        $body = View::render($path, $data);
        Http::setHeaders([
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
        $body = View::render('exception-screen');
        Http::setHeaders([
            500,
            "Content-type: text/html"
        ]);
        $this->output($body);
    }
}