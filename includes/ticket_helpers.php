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
    return [
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
    ];
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

function fetchTickets(PDO $pdo): array
{
    $sql = 'SELECT q.id, q.change_id, q.title, q.change_stage, q.change_status, q.change_type,
                   q.created_time, rs.name AS release_status,
                   s.name AS sprint_name,
                   mo.name AS owner_name,
                   mq.name AS qa_name
            FROM qa_data q
            INNER JOIN new_sprint s ON s.id = q.new_sprint_id
            INNER JOIN release_status rs ON rs.id = q.release_status_id
            INNER JOIN `user` mo ON mo.id = q.owner_user_id
            LEFT JOIN `user` mq ON mq.id = q.qa_user_id
            ORDER BY q.id ASC';
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();
    return array_map('formatTicketForFrontend', $rows);
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
        'releaseStatus'
    );
}

function ticketPayloadFromRequest(array $body, bool $isUpdate): array
{
    try {
        return buildTicketPayload($body, $isUpdate);
    } catch (TicketValidationException $e) {
        jsonError($e->getMessage());
    }
}

function insertTicket(PDO $pdo, array $fields): int
{
    $sprintId = findOrCreateSprint($pdo, $fields['sprint']);
    $ownerId = resolveOwnerMemberId($pdo, $fields['owner']);
    $qaId = resolveQaMemberId($pdo, $fields['qa']);
    $releaseStatusId = resolveReleaseStatusId($pdo, $fields['releaseStatus']);

    $stmt = $pdo->prepare(
        'INSERT INTO qa_data (new_sprint_id, change_id, title, owner_user_id, qa_user_id, change_stage, change_status, change_type, created_time, release_status_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
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
    ]);

    return (int) $pdo->lastInsertId();
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
         change_stage = ?, change_status = ?, change_type = ?, created_time = ?, release_status_id = ?
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
        $id,
    ]);
}

function handleDbException(PDOException $e): void
{
    logServerError($e);
    if ((int) $e->errorInfo[1] === 1062) {
        jsonError('A ticket with this Change ID already exists.');
    }
    jsonError('Unable to complete the request.', 500, false);
}
