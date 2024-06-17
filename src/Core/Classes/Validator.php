<?php

namespace App\Core\Classes;

use app\Core\Classes\Request;

class Validator
{
    protected array $attributes = [];
    protected array $errors = [];

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function set(array $data): array
    {
        $values = new Request();

        foreach ($data as $field => $validation) {
            $value = $values[$field] ?? null;
            $this->validateField($value, $validation, $field);
        }

        return $this->errors;
    }

    protected function validateField(mixed $value, string $validation, string $field): void
    {
        $pieces = explode('|', $validation);

        foreach ($pieces as $validate) {
            if ($pieces[0] === 'optional' && empty($value)) {
                break;
            }

            if ($validate === 'optional') {
                continue;
            }

            $options = explode(":", $validate);
            $result = count($options) > 1
                ? $this->validateValue($field, $options[0], $value, $options[1])
                : $this->validateValue($field, $options[0], $value);

            if ($result !== true) {
                $this->errors[] = $result;
            }
        }
    }

    protected function validateValue(string $field, string $type, $value, $parameter = null): bool|string
    {
        $result = false;

        switch ($type) {
            case 'bool':
                $result = in_array($value, [true, false, 0, 1, '0', '1'], true);
                break;
            case 'max':
                if (is_string($value)) {
                    $result = strlen($value) <= (int)$parameter;
                }
                break;
            case 'min':
                if (is_string($value)) {
                    $result = strlen($value) >= (int)$parameter;
                }
                break;
            case 'required':
                $result = !empty($value);
                break;
            case 'email':
                $result = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                break;
            case 'numeric':
                $result = is_numeric($value);
                break;
            case 'url':
                $result = filter_var($value, FILTER_VALIDATE_URL) !== false;
                break;
            case 'regex':
                $result = preg_match($parameter, $value) === 1;
                break;
            case 'date':
                $result = strtotime($value) !== false;
                break;
            case 'between':
                [$min, $max] = explode(',', $parameter);
                $result = (is_numeric($value) && $value >= $min && $value <= $max);
                break;
            default:
                $result = false;
                break;
        }

        if (!$result) {
            return $this->message($field, $type, $parameter, $value);
        }

        return true;
    }

    protected function message(string $field, string $type, $parameter = null, $value = null): string
    {
        $field = htmlspecialchars($this->attributes[$field] ?? $field, ENT_QUOTES, 'UTF-8');
        $parameter = htmlspecialchars((string)$parameter, ENT_QUOTES, 'UTF-8');
        $value = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');

        return match ($type) {
            'bool' => "The field '$field' only supports boolean values.",
            'max' => "The field '$field' supports a maximum length of $parameter characters.",
            'min' => "The field '$field' must be at least $parameter characters long.",
            'required' => "The field '$field' is required.",
            'email' => "The field '$field' must be a valid email address.",
            'numeric' => "The field '$field' must be a number.",
            'url' => "The field '$field' must be a valid URL.",
            'regex' => "The field '$field' does not match the required format.",
            'date' => "The field '$field' must be a valid date.",
            'between' => "The field '$field' must be between $parameter.",
            default => "The field '$field' has an invalid value.",
        };
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function sanitizeInput(string $string): bool
    {
        $string = trim($string);

        if (preg_match('/<!--.*?-->/s', $string)) {
            return true;
        }

        $maliciousTags = [
            'script', 'noscript', 'html', 'body', 'iframe', 'embed', 'object', 'form', 'input', 'button', 'select', 'textarea', 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'svg', 'math', 'link', 'meta', 'style', 'base', 'applet', 'canvas', 'video', 'audio', 'source', 'img'
        ];

        if (preg_match('/<\s*\/?(' . implode('|', $maliciousTags) . ')(\s+|>|\/>)/i', $string)) {
            return true;
        }

        if (preg_match('/<\s*\?\s*php/i', $string)) {
            return true;
        }

        $onEventAttributes = '/\bon[a-z]+ *=/i';
        if (preg_match($onEventAttributes, $string)) {
            return true;
        }

        if (preg_match('/<(svg|math|g|rect|circle|path|line|text).*?on[a-z]+ *=/i', $string)) {
            return true;
        }

        $entityAttributes = '/&[a-zA-Z0-9#]+;/';
        if (preg_match($entityAttributes, $string)) {
            return true;
        }

        if (preg_match('/(javascript|data|vbscript):/i', $string)) {
            return true;
        }

        return false;
    }
}
