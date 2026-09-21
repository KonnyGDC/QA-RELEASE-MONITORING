<?php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../../includes/validation.php';
require_once __DIR__ . '/../../includes/ticket_helpers.php';
require_once __DIR__ . '/../../includes/ticket_history.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

$body = readJsonBody();
$id = requirePositiveInt($body['id'] ?? null, 'ticket id');

try {
    $pdo = assertDatabaseConnection();
    dbTransaction($pdo, function (PDO $pdo) use ($id) {
        $snapshot = fetchTicketRowById($pdo, $id);
        if ($snapshot === null) {
            throw new TicketNotFoundException();
        }
        if (ticketHistoryTableExists($pdo)) {
            recordTicketHistory(
                $pdo,
                $id,
                $snapshot['Change ID'],
                'deleted',
                ['snapshot' => $snapshot]
            );
        }
        $stmt = $pdo->prepare('DELETE FROM qa_data WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) {
            throw new TicketNotFoundException();
        }
    });

    jsonSuccess(['id' => $id]);
} catch (TicketNotFoundException $e) {
    jsonError('Ticket not found.', 404);
} catch (PDOException $e) {
    logServerError($e);
    jsonError('Unable to delete ticket.', 500, false);
}
