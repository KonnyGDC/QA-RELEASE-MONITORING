<?php

const RELEASE_STATUSES = ['Released', 'For release', 'Not in release'];
const CHANGE_TYPES = ['Emergency', 'Normal', 'Standard'];
const USER_ROLES = ['Developer', 'QA'];
/** @deprecated use USER_ROLES */
const MEMBER_ROLES = USER_ROLES;

class TicketValidationException extends Exception
{
}

class TicketNotFoundException extends Exception
{
}

function formatTicketForFrontend(array $row): array
{
    $ticket = [
        'id' => (int) $row['id'],
        'Sprint' => $row['sprint_name'],
        'Change ID' => $row['change_id'],
        'Title' => $row['title'],
        'Change Owner' => $row['owner_name'],
        'Assigned QA' => $row['qa_name'] ?? 'Not Assigned',
        'Change Stage' => $row['change_stage'],
        'Change Status' => $row['change_status'],
        'Change Type' => $row['change_type'],
        'Created Time' => formatCreatedTimeDisplay($row['created_time']),
        'Release Status' => $row['release_status'],
        'Remarks' => $row['remarks'] ?? '',
    ];
    if (!empty($row['original_sprint_name'])) {
        $ticket['Original Sprint'] = $row['original_sprint_name'];
    }

    return $ticket;
}

function normalizeRemarksInput($value): ?string
{
    if ($value === null) {
        return null;
    }
    $str = trim((string) $value);
    if ($str === '') {
        return null;
    }
    if (mb_strlen($str) > 10000) {
        throw new TicketValidationException('Remarks is too long (max 10000 characters).');
    }

    return $str;
}

function appendRemarkLine(?string $existing, string $line): string
{
    $existing = trim((string) ($existing ?? ''));
    if ($existing === '') {
        return $line;
    }

    return $existing . "\n" . $line;
}

function transferRemarkTimestamp(): string
{
    return date('m-d-Y G:i');
}

function appendReturnLogLine(?string $existing, string $originalSprint, string $currentSprint, string $userNote): string
{
    $timestamp = transferRemarkTimestamp();
    $line = "[{$timestamp}] Returned back to original Sprint ({$originalSprint}) from {$currentSprint}.";
    if (trim($userNote) !== '') {
        $line .= ' Remarks: ' . trim($userNote);
    }

    return appendRemarkLine($existing, $line);
}

function appendTransferOutLogLine(?string $existing, string $targetSprint, string $changeId, string $userNote): string
{
    $timestamp = transferRemarkTimestamp();
    $line = "[{$timestamp}] Transferred to {$targetSprint} (Ticket ID: {$changeId}).";
    if (trim($userNote) !== '') {
        $line .= ' Remarks: ' . trim($userNote);
    }

    return appendRemarkLine($existing, $line);
}

function appendTransferInLogLine(?string $existing, string $currentSprint, string $changeId, string $userNote): string
{
    $timestamp = transferRemarkTimestamp();
    $line = "[{$timestamp}] Transferred from {$currentSprint} (Original Ticket: {$changeId}).";
    if (trim($userNote) !== '') {
        $line .= ' Remarks: ' . trim($userNote);
    }

    return appendRemarkLine($existing, $line);
}

function formatCreatedTimeDisplay(string $datetime): string
{
    $ts = strtotime($datetime);
    if ($ts === false) {
        return $datetime;
    }
    return date('m-d-Y G:i', $ts);
}

function parseCreatedTimeInput(?string $input): string
{
    if ($input === null || trim($input) === '') {
        return date('Y-m-d H:i:s');
    }
    $input = trim($input);
    $ts = strtotime($input);
    if ($ts !== false) {
        return date('Y-m-d H:i:s', $ts);
    }
    $parts = preg_split('/\s+/', $input, 2);
    $datePart = $parts[0] ?? '';
    $timePart = $parts[1] ?? '00:00';
    $dateBits = explode('-', $datePart);
    if (count($dateBits) === 3 && strlen($dateBits[0]) <= 2) {
        $iso = sprintf(
            '%04d-%02d-%02d %s:00',
            (int) $dateBits[2],
            (int) $dateBits[0],
            (int) $dateBits[1],
            $timePart
        );
        $ts = strtotime($iso);
        if ($ts !== false) {
            return date('Y-m-d H:i:s', $ts);
        }
    }
    return date('Y-m-d H:i:s');
}

function ticketSelectSql(): string
{
    return 'SELECT q.id, q.change_id, q.title, q.change_stage, q.change_status, q.change_type,
                   q.created_time, q.remarks, q.new_sprint_id, q.original_sprint_id,
                   rs.name AS release_status,
                   s.name AS sprint_name,
                   os.name AS original_sprint_name,
                   mo.name AS owner_name,
                   mq.name AS qa_name
            FROM qa_data q
            INNER JOIN new_sprint s ON s.id = q.new_sprint_id
            LEFT JOIN new_sprint os ON os.id = q.original_sprint_id
            INNER JOIN release_status rs ON rs.id = q.release_status_id
            INNER JOIN `user` mo ON mo.id = q.owner_user_id
            LEFT JOIN `user` mq ON mq.id = q.qa_user_id';
}

function fetchTickets(PDO $pdo): array
{
    $sql = ticketSelectSql() . ' ORDER BY q.id ASC';
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();
    return array_map('formatTicketForFrontend', $rows);
}

function fetchTicketDbRowById(PDO $pdo, int $id): ?array
{
    $sql = ticketSelectSql() . ' WHERE q.id = ? LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function fetchTicketRowById(PDO $pdo, int $id): ?array
{
    $row = fetchTicketDbRowById($pdo, $id);

    return $row ? formatTicketForFrontend($row) : null;
}

function fetchSprintNames(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT name FROM new_sprint ORDER BY name ASC');
    return array_column($stmt->fetchAll(), 'name');
}

function fetchMemberNamesByRole(PDO $pdo, string $role): array
{
    $stmt = $pdo->prepare('SELECT name FROM `user` WHERE role = ? ORDER BY name ASC');
    $stmt->execute([$role]);
    return array_column($stmt->fetchAll(), 'name');
}

function findSprintIdByName(PDO $pdo, string $name): ?int
{
    $stmt = $pdo->prepare('SELECT id FROM new_sprint WHERE name = ? LIMIT 1');
    $stmt->execute([$name]);
    $row = $stmt->fetch();
    return $row ? (int) $row['id'] : null;
}

function findUserIdByNameAndRole(PDO $pdo, string $name, string $role): ?int
{
    $stmt = $pdo->prepare('SELECT id FROM `user` WHERE name = ? AND role = ? LIMIT 1');
    $stmt->execute([$name, $role]);
    $row = $stmt->fetch();
    return $row ? (int) $row['id'] : null;
}

function findReleaseStatusIdByName(PDO $pdo, string $name): ?int
{
    $stmt = $pdo->prepare('SELECT id FROM release_status WHERE name = ? LIMIT 1');
    $stmt->execute([$name]);
    $row = $stmt->fetch();
    return $row ? (int) $row['id'] : null;
}

function findOrCreateSprint(PDO $pdo, string $name): int
{
    $id = findSprintIdByName($pdo, $name);
    if ($id !== null) {
        return $id;
    }
    $stmt = $pdo->prepare('INSERT INTO new_sprint (name) VALUES (?)');
    $stmt->execute([$name]);
    return (int) $pdo->lastInsertId();
}

function findOrCreateUser(PDO $pdo, string $name, string $role): int
{
    $id = findUserIdByNameAndRole($pdo, $name, $role);
    if ($id !== null) {
        return $id;
    }
    $stmt = $pdo->prepare('INSERT INTO `user` (name, role) VALUES (?, ?)');
    $stmt->execute([$name, $role]);
    return (int) $pdo->lastInsertId();
}

/** @deprecated use findUserIdByNameAndRole */
function findMemberIdByNameAndRole(PDO $pdo, string $name, string $role): ?int
{
    return findUserIdByNameAndRole($pdo, $name, $role);
}

/** @deprecated use findOrCreateUser */
function findOrCreateMember(PDO $pdo, string $name, string $role): int
{
    return findOrCreateUser($pdo, $name, $role);
}

function resolveReleaseStatusId(PDO $pdo, string $releaseStatusName): int
{
    $id = findReleaseStatusIdByName($pdo, $releaseStatusName);
    if ($id === null) {
        throw new TicketValidationException('Invalid Release Status.');
    }
    return $id;
}

function resolveQaMemberId(PDO $pdo, ?string $qaName): ?int
{
    $qaName = trim((string) ($qaName ?? ''));
    if ($qaName === '' || strcasecmp($qaName, 'Not Assigned') === 0) {
        return null;
    }
    $id = findUserIdByNameAndRole($pdo, $qaName, 'QA');
    if ($id === null) {
        $id = findOrCreateUser($pdo, $qaName, 'QA');
    }
    return $id;
}

function resolveOwnerMemberId(PDO $pdo, string $ownerName): int
{
    $ownerName = trim($ownerName);
    $id = findUserIdByNameAndRole($pdo, $ownerName, 'Developer');
    if ($id === null) {
        $id = findOrCreateUser($pdo, $ownerName, 'Developer');
    }
    return $id;
}

function ticketFieldRequired($value, string $fieldName, int $maxLen): string
{
    $str = trim((string) ($value ?? ''));
    if ($str === '') {
        throw new TicketValidationException("{$fieldName} is required.");
    }
    if (mb_strlen($str) > $maxLen) {
        throw new TicketValidationException("{$fieldName} is too long (max {$maxLen} characters).");
    }

    return $str;
}

function ticketFieldEnum($value, string $fieldName, array $allowed): string
{
    $str = trim((string) ($value ?? ''));
    if (!in_array($str, $allowed, true)) {
        throw new TicketValidationException("Invalid {$fieldName}.");
    }

    return $str;
}

function buildTicketPayload(array $body, bool $isUpdate): array
{
    $sprint = ticketFieldRequired($body['Sprint'] ?? $body['sprint'] ?? null, 'Sprint', 100);
    $changeId = ticketFieldRequired($body['Change ID'] ?? $body['change_id'] ?? null, 'Change ID', 32);
    $title = ticketFieldRequired($body['Title'] ?? $body['title'] ?? null, 'Title', 500);
    $owner = ticketFieldRequired($body['Change Owner'] ?? $body['change_owner'] ?? null, 'Change Owner', 150);
    $qa = trim((string) ($body['Assigned QA'] ?? $body['assigned_qa'] ?? ''));
    $changeType = ticketFieldEnum($body['Change Type'] ?? $body['change_type'] ?? null, 'Change Type', CHANGE_TYPES);
    $releaseStatus = ticketFieldEnum(
        $body['Release Status'] ?? $body['release_status'] ?? null,
        'Release Status',
        RELEASE_STATUSES
    );

    if ($isUpdate) {
        $changeStage = ticketFieldRequired($body['Change Stage'] ?? $body['change_stage'] ?? null, 'Change Stage', 80);
        $changeStatus = ticketFieldRequired($body['Change Status'] ?? $body['change_status'] ?? null, 'Change Status', 80);
        $createdTime = parseCreatedTimeInput($body['Created Time'] ?? $body['created_time'] ?? null);
    } else {
        $changeStage = trim((string) ($body['Change Stage'] ?? $body['change_stage'] ?? 'Development'));
        if ($changeStage === '') {
            $changeStage = 'Development';
        }
        if (mb_strlen($changeStage) > 80) {
            throw new TicketValidationException('Change Stage is too long (max 80 characters).');
        }
        $changeStatus = trim((string) ($body['Change Status'] ?? $body['change_status'] ?? 'Active'));
        if ($changeStatus === '') {
            $changeStatus = 'Active';
        }
        if (mb_strlen($changeStatus) > 80) {
            throw new TicketValidationException('Change Status is too long (max 80 characters).');
        }
        $createdTime = parseCreatedTimeInput($body['Created Time'] ?? $body['created_time'] ?? null);
    }

    $remarks = normalizeRemarksInput($body['Remarks'] ?? $body['remarks'] ?? null);

    return compact(
        'sprint',
        'changeId',
        'title',
        'owner',
        'qa',
        'changeStage',
        'changeStatus',
        'changeType',
        'createdTime',
        'releaseStatus',
        'remarks'
    );
}

function ticketPayloadFromRequest(array $body, bool $isUpdate): array
{
    try {
        return buildTicketPayload($body, $isUpdate);
    } catch (TicketValidationException $e) {
        jsonError($e->getMessage());
    }

    throw new RuntimeException('ticketPayloadFromRequest: unreachable');
}

function insertTicket(PDO $pdo, array $fields): int
{
    $sprintId = findOrCreateSprint($pdo, $fields['sprint']);
    $ownerId = resolveOwnerMemberId($pdo, $fields['owner']);
    $qaId = resolveQaMemberId($pdo, $fields['qa']);
    $releaseStatusId = resolveReleaseStatusId($pdo, $fields['releaseStatus']);

    $originalSprintId = $fields['originalSprintId'] ?? null;

    $stmt = $pdo->prepare(
        'INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id, remarks, original_sprint_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $sprintId,
        $fields['changeId'],
        $fields['title'],
        $ownerId,
        $qaId,
        $fields['changeStage'],
        $fields['changeStatus'],
        $fields['changeType'],
        $fields['createdTime'],
        $releaseStatusId,
        $fields['remarks'] ?? null,
        $originalSprintId,
    ]);

    return (int) $pdo->lastInsertId();
}

function findTicketIdByChangeIdAndSprint(PDO $pdo, string $changeId, string $sprintName): ?int
{
    $stmt = $pdo->prepare(
        'SELECT q.id FROM qa_data q
         INNER JOIN new_sprint s ON s.id = q.new_sprint_id
         WHERE q.change_id = ? AND s.name = ?
         LIMIT 1'
    );
    $stmt->execute([trim($changeId), trim($sprintName)]);
    $row = $stmt->fetch();

    return $row ? (int) $row['id'] : null;
}

/** @deprecated use findTicketIdByChangeIdAndSprint */
function findTicketIdByChangeId(PDO $pdo, string $changeId): ?int
{
    $stmt = $pdo->prepare('SELECT id FROM qa_data WHERE change_id = ? LIMIT 1');
    $stmt->execute([trim($changeId)]);
    $row = $stmt->fetch();

    return $row ? (int) $row['id'] : null;
}

/**
 * @return array{id: int, mode: 'insert'|'update'}
 */
function upsertTicketFromImport(PDO $pdo, array $row): array
{
    $changeId = (string) ($row['Change ID'] ?? $row['change_id'] ?? '');
    $sprintName = trim((string) ($row['Sprint'] ?? $row['sprint'] ?? ''));
    if ($sprintName === '') {
        $sprintName = 'Sprint 10';
    }
    $existingId = findTicketIdByChangeIdAndSprint($pdo, $changeId, $sprintName);
    if ($existingId !== null) {
        $fields = buildTicketPayload($row, true);
        updateTicketById($pdo, $existingId, $fields);

        return ['id' => $existingId, 'mode' => 'update'];
    }

    $fields = buildTicketPayload($row, false);
    $newId = insertTicket($pdo, $fields);

    return ['id' => $newId, 'mode' => 'insert'];
}

function updateTicketById(PDO $pdo, int $id, array $fields): void
{
    $check = $pdo->prepare('SELECT id FROM qa_data WHERE id = ? LIMIT 1');
    $check->execute([$id]);
    if (!$check->fetch()) {
        throw new TicketNotFoundException();
    }

    $sprintId = findOrCreateSprint($pdo, $fields['sprint']);
    $ownerId = resolveOwnerMemberId($pdo, $fields['owner']);
    $qaId = resolveQaMemberId($pdo, $fields['qa']);
    $releaseStatusId = resolveReleaseStatusId($pdo, $fields['releaseStatus']);

    $stmt = $pdo->prepare(
        'UPDATE qa_data SET new_sprint_id = ?, change_id = ?, title = ?, owner_user_id = ?, qa_user_id = ?,
         change_stage = ?, change_status = ?, change_type = ?, created_time = ?, release_status_id = ?, remarks = ?
         WHERE id = ?'
    );
    $stmt->execute([
        $sprintId,
        $fields['changeId'],
        $fields['title'],
        $ownerId,
        $qaId,
        $fields['changeStage'],
        $fields['changeStatus'],
        $fields['changeType'],
        $fields['createdTime'],
        $releaseStatusId,
        $fields['remarks'] ?? null,
        $id,
    ]);
}

/**
 * @param array{changeStage?: string, changeStatus?: string, releaseStatus?: string} $modalFields
 * @return array{mode: 'return'|'clone', sourceId: int, targetId?: int}
 */
function transferTicketSprint(PDO $pdo, int $id, string $targetSprintName, string $transferReason, array $modalFields = []): array
{
    $row = fetchTicketDbRowById($pdo, $id);
    if ($row === null) {
        throw new TicketNotFoundException();
    }

    $currentSprint = $row['sprint_name'];
    $changeId = $row['change_id'];
    $targetSprintName = trim($targetSprintName);
    if ($targetSprintName === '') {
        throw new TicketValidationException('Target sprint is required.');
    }
    if ($targetSprintName === $currentSprint) {
        throw new TicketValidationException('Target sprint must be different from current sprint.');
    }

    $currentSprintId = (int) $row['new_sprint_id'];
    $originalSprintId = $row['original_sprint_id'] !== null ? (int) $row['original_sprint_id'] : null;

    if ($originalSprintId === null) {
        $originalSprintId = $currentSprintId;
        $stmt = $pdo->prepare('UPDATE qa_data SET original_sprint_id = ? WHERE id = ?');
        $stmt->execute([$originalSprintId, $id]);
    }

    $originalSprintName = $row['original_sprint_name'] ?? null;
    if ($originalSprintName === null || $originalSprintName === '') {
        $stmt = $pdo->prepare('SELECT name FROM new_sprint WHERE id = ? LIMIT 1');
        $stmt->execute([$originalSprintId]);
        $originalSprintName = (string) ($stmt->fetchColumn() ?: $currentSprint);
    }

    $targetSprintId = findOrCreateSprint($pdo, $targetSprintName);
    $existingRemarks = $row['remarks'] ?? '';

    $changeStage = trim((string) ($modalFields['changeStage'] ?? ''));
    if ($changeStage === '') {
        $changeStage = $row['change_stage'];
    }
    $changeStatus = trim((string) ($modalFields['changeStatus'] ?? ''));
    if ($changeStatus === '') {
        $changeStatus = $row['change_status'];
    }
    $releaseStatusName = trim((string) ($modalFields['releaseStatus'] ?? ''));
    if ($releaseStatusName === '') {
        $releaseStatusName = $row['release_status'];
    }
    $releaseStatusId = resolveReleaseStatusId($pdo, $releaseStatusName);

    if ($targetSprintName === $originalSprintName) {
        $stubId = findTicketIdByChangeIdAndSprint($pdo, $changeId, $originalSprintName);
        if ($stubId !== null && $stubId !== $id) {
            $stubRow = fetchTicketDbRowById($pdo, $stubId);
            if ($stubRow !== null) {
                $existingRemarks = appendRemarkLine($existingRemarks, trim((string) ($stubRow['remarks'] ?? '')));
            }
            $del = $pdo->prepare('DELETE FROM qa_data WHERE id = ?');
            $del->execute([$stubId]);
        }

        $newRemarks = appendReturnLogLine($existingRemarks, $originalSprintName, $currentSprint, $transferReason);
        $stmt = $pdo->prepare(
            'UPDATE qa_data SET new_sprint_id = ?, change_stage = ?, change_status = ?, release_status_id = ?, remarks = ? WHERE id = ?'
        );
        $stmt->execute([$originalSprintId, $changeStage, $changeStatus, $releaseStatusId, $newRemarks, $id]);

        return ['mode' => 'return', 'sourceId' => $id];
    }

    $duplicateId = findTicketIdByChangeIdAndSprint($pdo, $changeId, $targetSprintName);
    if ($duplicateId !== null) {
        throw new TicketValidationException('A ticket with this Change ID already exists in the target sprint.');
    }

    $sourceRemarks = appendTransferOutLogLine($existingRemarks, $targetSprintName, $changeId, $transferReason);
    $stmt = $pdo->prepare('UPDATE qa_data SET remarks = ? WHERE id = ?');
    $stmt->execute([$sourceRemarks, $id]);

    $cloneRemarks = appendTransferInLogLine(null, $currentSprint, $changeId, $transferReason);
    $cloneFields = [
        'sprint' => $targetSprintName,
        'changeId' => $changeId,
        'title' => $row['title'],
        'owner' => $row['owner_name'],
        'qa' => $row['qa_name'] ?? '',
        'changeStage' => $changeStage,
        'changeStatus' => $changeStatus,
        'changeType' => $row['change_type'],
        'createdTime' => date('Y-m-d H:i:s'),
        'releaseStatus' => $releaseStatusName,
        'remarks' => $cloneRemarks,
        'originalSprintId' => $originalSprintId,
    ];
    $newId = insertTicket($pdo, $cloneFields);

    return ['mode' => 'clone', 'sourceId' => $id, 'targetId' => $newId];
}

function handleDbException(PDOException $e): void
{
    logServerError($e);
    if ((int) $e->errorInfo[1] === 1062) {
        jsonError('A ticket with this Change ID already exists in this sprint.');
    }
    jsonError('Unable to complete the request.', 500, false);
}
