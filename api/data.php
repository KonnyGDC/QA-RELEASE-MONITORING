<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/../includes/ticket_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Method not allowed.', 405);
}

$status = getDatabaseStatus();
if (!$status['connected']) {
    jsonError($status['message'], 503, false);
}

try {
    $pdo = getDb();
    jsonSuccess([
        'tickets' => fetchTickets($pdo),
        'sprints' => fetchSprintNames($pdo),
        'developers' => fetchMemberNamesByRole($pdo, 'Developer'),
        'qaMembers' => fetchMemberNamesByRole($pdo, 'QA'),
    ], 200, true);
} catch (PDOException $e) {
    logServerError($e);
    jsonError('Unable to retrieve data.', 500, false);
}