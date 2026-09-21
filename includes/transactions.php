<?php

/**
 * Run a callback inside a PDO transaction.
 * Commits when the callback completes; rolls back on any Throwable.
 *
 * @template T
 * @param callable(PDO): T $callback
 * @return T
 * @throws Throwable
 */
function dbTransaction(PDO $pdo, callable $callback)
{
    if ($pdo->inTransaction()) {
        return $callback($pdo);
    }

    $pdo->beginTransaction();

    try {
        $result = $callback($pdo);
        $pdo->commit();

        return $result;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}
