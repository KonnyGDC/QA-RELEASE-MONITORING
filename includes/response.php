<?php

function jsonSuccess($data = null, int $code = 200, bool $connected = true): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    $payload = [
        'success' => true,
        'connected' => $connected,
    ];
    if ($data !== null) {
        $payload['data'] = $data;
    }
    echo json_encode($payload);
    exit;
}

function jsonError(string $message, int $code = 400, bool $connected = true): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'connected' => $connected,
        'message' => $message,
    ]);
    exit;
}

function readJsonBody(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        jsonError('Invalid JSON request body.');
    }
    return $data;
}

function logServerError(Throwable $e): void
{
    error_log('[QA Release Monitoring] ' . $e->getMessage());
}
