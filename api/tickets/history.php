<?php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../../includes/validation.php';
require_once __DIR__ . '/../../includes/ticket_history.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Method not allowed.', 405);
}

$id = requirePositiveInt($_GET['id'] ?? null, 'ticket id');

try {
    $pdo = assertDatabaseConnection();
    if (!ticketHistoryTableExists($pdo)) {
        jsonError(
            'Ticket history is not available. Import database/migrate.sql to add the ticket_history table.',
            503
        );
    }

    $ticket = fetchTicketRowById($pdo, $id);
    if ($ticket === null) {
        jsonError('Ticket not found.', 404);
    }

    jsonSuccess([
        'ticket' => $ticket,
        'history' => fetchTicketHistoryForTicket($pdo, $id),
    ]);
} catch (PDOException $e) {
    logServerError($e);
    jsonError('Unable to load ticket history.', 500, false);
}
