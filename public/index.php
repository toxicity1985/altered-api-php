<?php

declare(strict_types=1);

// Autoriser uniquement GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    exit;
}

// récupérer l'id depuis l'URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$cardId = trim($uri, '/');

// validation stricte
if (!preg_match('/^ALT_[A-Z0-9]+_[A-Z]_[A-Z0-9]{2}_[0-9]{2}_[A-Z]_[0-9]+$/', $cardId)) {
    http_response_code(400);
    exit('Invalid card id');
}

// dossier des données (hors public)
$baseDir = realpath(__DIR__ . '/../community_database');

if ($baseDir === false) {
    http_response_code(500);
    exit('Server configuration error');
}

// extraire les segments
$parts = explode('_', $cardId);

$set   = $parts[1]; // CYCLONE
$code1 = $parts[3]; // YZ
$code2 = $parts[4]; // 68

$filePath = $baseDir . DIRECTORY_SEPARATOR .
    $set . DIRECTORY_SEPARATOR .
    $code1 . DIRECTORY_SEPARATOR .
    $code2 . DIRECTORY_SEPARATOR .
    $cardId . '.json';

$realFile = realpath($filePath);

// sécurité path traversal
if ($realFile === false || strpos($realFile, $baseDir) !== 0) {
    http_response_code(404);
    exit('Card not found');
}

if (!is_file($realFile) || !is_readable($realFile)) {
    http_response_code(404);
    exit('Card not found');
}

// headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// envoyer le JSON
readfile($realFile);