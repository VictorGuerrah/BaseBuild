<?php

namespace App\Core\Classes;

class Request
{
    private array $request = [];
    protected array $validate = [];
    protected array $files = [];
    protected array $headers = [];
    protected array $cookies = [];
    protected string $host = '';
    protected string $method = '';
    protected string $uri = '';
    protected string $base = '';
    protected string $protocol = '';

    public function __construct()
    {
        $this->request = $_REQUEST ?? [];
        $this->headers = getallheaders();
        $this->host = isset($_SERVER['HTTP_HOST']) ? filter_var(trim(htmlspecialchars($_SERVER['HTTP_HOST']))) : '';
        $this->method = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->base = isset($_SERVER['SCRIPT_NAME']) ? implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/' : '';
        $this->protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $this->cookies = $_COOKIE;
        if (!empty($_FILES)) {
            $this->setFiles($_FILES);
        }

        $script = explode('/', filter_var(trim($this->base, '/'), FILTER_SANITIZE_URL));
        $uri = isset($_SERVER['REQUEST_URI']) ? explode('/', filter_var(trim($_SERVER['REQUEST_URI'], '/'), FILTER_SANITIZE_URL)) : [];

        $this->uri = implode('/', array_diff($uri, $script));
    }

    public function setFiles(array $files = []): void
    {
        foreach ($files as $key => $value) {
            $this->files[$key] = $value;
        }
    }

    public function setRequest(string $key, string|array $value): void
    {
        $this->request[$key] = $value;
    }

    public function base(): string
    {
        return $this->base;
    }

    public function url(): string
    {
        return $this->protocol . '://' . $this->host . '/' . $this->uri;
    }

    public function uriToString(): string
    {
        return $this->uri;
    }

    public function uri(): string
    {
        return explode('?', $this->uri)[0];
    }

    public function method(): string
    {
        return $this->method;
    }

    public function join(string $uri): string
    {
        $uri = implode('/', explode('/', $uri));
        return $this->url() . $uri;
    }

    public function validate(array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if (!isset($this->request[$field])) {
                $errors[$field] = "$field is required";
            }
        }
        return $errors;
    }

    public function fails(array $rules): bool
    {
        return !empty($this->validate);
    }

    public function all(): array
    {
        return $this->request;
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->request, array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->request, array_flip($keys));
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]);
    }

    public function file(string $key): ?array
    {
        return $this->hasFile($key) ? $this->files[$key] : null;
    }

    public function has(string $key): bool
    {
        return isset($this->request[$key]);
    }

    public function get(string $key, $default = null)
    {
        return $this->has($key) ? $this->request[$key] : $default;
    }

    public function getHeader(string $header): ?string
    {
        return $this->headers[$header] ?? null;
    }

    public function getCookie(string $key): ?string
    {
        return $this->cookies[$key] ?? null;
    }

    public static function make(): self
    {
        return new self();
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }
}
