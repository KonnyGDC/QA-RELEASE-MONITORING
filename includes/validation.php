<?php

function requirePositiveInt($value, string $fieldName): int
{
    if ($value === null || $value === '' || !is_numeric($value)) {
        jsonError("Invalid {$fieldName}.");
    }
    $id = (int) $value;
    if ($id < 1) {
        jsonError("Invalid {$fieldName}.");
    }
    return $id;
}

function requireNonEmptyString($value, string $fieldName, int $maxLen): string
{
    $str = trim((string) ($value ?? ''));
    if ($str === '') {
        jsonError("{$fieldName} is required.");
    }
    if (mb_strlen($str) > $maxLen) {
        jsonError("{$fieldName} is too long (max {$maxLen} characters).");
    }
    return $str;
}

function requireEnum($value, string $fieldName, array $allowed): string
{
    $str = trim((string) ($value ?? ''));
    if (!in_array($str, $allowed, true)) {
        jsonError("Invalid {$fieldName}.");
    }
    return $str;
}

function optionalEnumOrNull($value, string $fieldName, array $allowed): ?string
{
    if ($value === null || $value === '') {
        return null;
    }
    return requireEnum($value, $fieldName, $allowed);
}
