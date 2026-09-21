<?php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../../includes/validation.php';
require_once __DIR__ . '/../../includes/ticket_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

$body = readJsonBody();
$name = requireNonEmptyString($body['name'] ?? null, 'Name', 150);
$role = requireEnum($body['role'] ?? null, 'Role', MEMBER_ROLES);

try {
    $pdo = assertDatabaseConnection();
    $existing = findUserIdByNameAndRole($pdo, $name, $role);
    if ($existing !== null) {
        jsonSuccess(['id' => $existing, 'name' => $name, 'role' => $role, 'existing' => true]);
    }

    $id = dbTransaction($pdo, function (PDO $pdo) use ($name, $role) {
        return findOrCreateUser($pdo, $name, $role);
    });

    jsonSuccess(['id' => $id, 'name' => $name, 'role' => $role]);
} catch (PDOException $e) {
    handleDbException($e);
}
