<?php


function asset(string $path): void
{
    $file = BASE_PUBLIC . 'assets/' . ($path[0] == '/' ? mb_substr($path, 1) : $path);
    if (!file_exists($file)) {
        throw new \Exception("File not found", 404);
    }
    $relativePath = 'assets/' . ($path[0] == '/' ? mb_substr($path, 1) : $path);
    echo $relativePath;
}
