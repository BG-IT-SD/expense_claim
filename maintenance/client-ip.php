<?php

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('X-Content-Type-Options: nosniff');

echo json_encode([
    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? '',
    'forwarded_for' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
], JSON_UNESCAPED_SLASHES);
