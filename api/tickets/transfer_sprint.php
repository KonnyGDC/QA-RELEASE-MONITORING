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
$targetSprint = requireNonEmptyString($body['targetSprint'] ?? null, 'target sprint', 100);
$transferReason = trim((string) ($body['transferReason'] ?? ''));
$modalFields = [
    'changeStage' => trim((string) ($body['changeStage'] ?? '')),
    'changeStatus' => trim((string) ($body['changeStatus'] ?? '')),
    'releaseStatus' => trim((string) ($body['releaseStatus'] ?? '')),
];

try {
    $pdo = assertDatabaseConnection();
    $result = null;
    $sourceBefore = fetchTicketRowById($pdo, $id);

    dbTransaction($pdo, function (PDO $pdo) use ($id, $targetSprint, $transferReason, $modalFields, &$result, $sourceBefore) {
        if ($sourceBefore === null) {
            throw new TicketNotFoundException();
        }

        $result = transferTicketSprint($pdo, $id, $targetSprint, $transferReason, $modalFields);

        if (!ticketHistoryTableExists($pdo)) {
            return;
        }

        $sourceAfter = fetchTicketRowById($pdo, $result['sourceId']);
        if ($sourceAfter !== null) {
            $changes = diffTicketSnapshots($sourceBefore, $sourceAfter);
            recordTicketHistory(
                $pdo,
                $result['sourceId'],
                $sourceAfter['Change ID'],
                'transferred',
                ['changes' => $changes, 'snapshot' => $sourceAfter, 'transferMode' => $result['mode']]
            );
        }

        if ($result['mode'] === 'clone' && isset($result['targetId'])) {
            $newTicket = fetchTicketRowById($pdo, $result['targetId']);
            if ($newTicket !== null) {
                recordTicketHistory(
                    $pdo,
                    $result['targetId'],
                    $newTicket['Change ID'],
                    'created',
                    ['snapshot' => $newTicket, 'transferClone' => true]
                );
            }
        }
    });

    $pdo = getDb();
    $ticket = fetchTicketRowById($pdo, $result['sourceId']);
    $payload = [
        'mode' => $result['mode'],
        'id' => $result['sourceId'],
        'ticket' => $ticket,
    ];
    if ($result['mode'] === 'clone' && isset($result['targetId'])) {
        $payload['newTicket'] = fetchTicketRowById($pdo, $result['targetId']);
        $payload['newTicketId'] = $result['targetId'];
    }

    jsonSuccess($payload);
} catch (TicketNotFoundException $e) {
    jsonError('Ticket not found.', 404);
} catch (TicketValidationException $e) {
    jsonError($e->getMessage());
} catch (PDOException $e) {
    handleDbException($e);
} catch (Throwable $e) {
    logServerError($e);
    jsonError('Unable to transfer ticket.', 500, false);
}
