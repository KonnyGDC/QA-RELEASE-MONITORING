<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/migration_helpers.php';

return static function (PDO $pdo): void {
    migrationAddColumnIfNotExists($pdo, 'qa_data', 'original_sprint_id', 'INT NULL');

    if (!migrationIndexExists($pdo, 'qa_data', 'idx_qa_data_original_sprint_id')) {
        $pdo->exec('ALTER TABLE qa_data ADD KEY idx_qa_data_original_sprint_id (original_sprint_id)');
    }

    migrationDropIndexIfExists($pdo, 'qa_data', 'uk_qa_data_change_id');

    if (!migrationIndexExists($pdo, 'qa_data', 'uk_qa_data_change_sprint')) {
        $pdo->exec('ALTER TABLE qa_data ADD UNIQUE KEY uk_qa_data_change_sprint (change_id, new_sprint_id)');
    }

    $fkCheck = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.table_constraints
         WHERE table_schema = DATABASE() AND table_name = ? AND constraint_name = ?'
    );
    $fkCheck->execute(['qa_data', 'fk_qa_data_original_sprint']);
    if ((int) $fkCheck->fetchColumn() === 0) {
        $pdo->exec(
            'ALTER TABLE qa_data
             ADD CONSTRAINT fk_qa_data_original_sprint
             FOREIGN KEY (original_sprint_id) REFERENCES new_sprint(id)
             ON DELETE SET NULL ON UPDATE CASCADE'
        );
    }

    $pdo->exec('UPDATE qa_data SET original_sprint_id = new_sprint_id WHERE original_sprint_id IS NULL');

    migrationRecordSchemaVersion($pdo, '2026.03.26.6');
};
