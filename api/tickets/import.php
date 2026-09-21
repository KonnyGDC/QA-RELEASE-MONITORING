<?php

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../../includes/validation.php';
require_once __DIR__ . '/../../includes/ticket_helpers.php';
require_once __DIR__ . '/../../includes/ticket_history.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

$body = readJsonBody();
$rows = $body['tickets'] ?? null;
if (!is_array($rows) || count($rows) === 0) {
    jsonError('No tickets to import.');
}

try {
    $pdo = assertDatabaseConnection();
    $summary = dbTransaction($pdo, function (PDO $pdo) use ($rows) {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                $skipped++;
                $errors[] = 'Row ' . ($index + 1) . ': invalid format.';
                continue;
            }

            try {
                $fields = buildTicketPayload($row, false);
            } catch (TicketValidationException $e) {
                $skipped++;
                $errors[] = 'Row ' . ($index + 1) . ': ' . $e->getMessage();
                continue;
            }

            try {
                $newId = insertTicket($pdo, $fields);
                if (ticketHistoryTableExists($pdo)) {
                    $snapshot = fetchTicketRowById($pdo, $newId);
                    if ($snapshot !== null) {
                        recordTicketHistory(
                            $pdo,
                            $newId,
                            $snapshot['Change ID'],
                            'imported',
                            ['snapshot' => $snapshot]
                        );
                    }
                }
                $imported++;
            } catch (PDOException $e) {
                if ((int) $e->errorInfo[1] === 1062) {
                    $skipped++;
                    $errors[] = 'Row ' . ($index + 1) . ': duplicate Change ID.';
                    continue;
                }
                throw $e;
            }
        }

        if ($imported === 0 && $skipped > 0) {
            throw new TicketValidationException('No tickets were imported. Fix the rows reported in errors.');
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    });

    jsonSuccess($summary);
} catch (TicketValidationException $e) {
    jsonError($e->getMessage());
} catch (PDOException $e) {
    handleDbException($e);
}
