<?php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../../includes/validation.php';
require_once __DIR__ . '/../../includes/ticket_helpers.php';
require_once __DIR__ . '/../../includes/ticket_history.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

$body = readJsonBody();
$ticket = $body['ticket'] ?? $body;
$id = requirePositiveInt($body['id'] ?? $ticket['id'] ?? null, 'ticket id');
$fields = ticketPayloadFromRequest($ticket, true);

try {
    $pdo = assertDatabaseConnection();
    dbTransaction($pdo, function (PDO $pdo) use ($id, $fields) {
        $before = fetchTicketRowById($pdo, $id);
        if ($before === null) {
            throw new TicketNotFoundException();
        }
        updateTicketById($pdo, $id, $fields);
        if (ticketHistoryTableExists($pdo)) {
            $after = fetchTicketRowById($pdo, $id);
            if ($after !== null) {
                $changes = diffTicketSnapshots($before, $after);
                recordTicketHistory(
                    $pdo,
                    $id,
                    $after['Change ID'],
                    'updated',
                    ['changes' => $changes, 'snapshot' => $after]
                );
            }
        }
    });

    jsonSuccess(['id' => $id]);
} catch (TicketNotFoundException $e) {
    jsonError('Ticket not found.', 404);
} catch (PDOException $e) {
    handleDbException($e);
}
