<?php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../../includes/validation.php';
require_once __DIR__ . '/../../includes/ticket_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

$body = readJsonBody();
$name = requireNonEmptyString($body['sprintName'] ?? $body['name'] ?? null, 'Sprint name', 100);

try {
    $pdo = assertDatabaseConnection();
    $existing = findSprintIdByName($pdo, $name);
    if ($existing !== null) {
        jsonSuccess(['id' => $existing, 'name' => $name, 'existing' => true]);
    }

    $id = dbTransaction($pdo, function (PDO $pdo) use ($name) {
        return findOrCreateSprint($pdo, $name);
    });

    jsonSuccess(['id' => $id, 'name' => $name]);
} catch (PDOException $e) {
    handleDbException($e);
}
