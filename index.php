<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$blocked = [
    '/config/',
    '/database/',
    '/vendor/',
    '/storage/',
    '/bootstrap/',
    '/tests/',
    '/routes/',
    '/resources/',
    '/app/',
    '/.env',
    '/.git',
];

foreach ($blocked as $path) {
    if (stripos($uri, $path) === 0) {
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>403 - Доступ запрещён</h1><p>У вас нет доступа к этому ресурсу.</p></body></html>';
        exit;
    }
}

// Перенаправление в public/
require __DIR__ . '/public/index.php';
