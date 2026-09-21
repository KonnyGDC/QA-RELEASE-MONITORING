<?php

require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/ticket_helpers.php';

function runHelperUnitTests(callable $assertTrue): void
{
    $assertTrue('parseCreatedTimeInput MM-DD-YYYY', function () {
        $out = parseCreatedTimeInput('04-25-2026 10:20');
        return $out === '2026-04-25 10:20:00';
    });

    $assertTrue('buildTicketPayload valid row', function () {
        $fields = buildTicketPayload([
            'Sprint' => 'Sprint 10',
            'Change ID' => 'CH-UNIT-1',
            'Title' => 'Unit test',
            'Change Owner' => 'Someone',
            'Assigned QA' => 'Not Assigned',
            'Change Type' => 'Normal',
            'Release Status' => 'For release',
        ], false);
        return $fields['changeId'] === 'CH-UNIT-1';
    });

    $assertTrue('buildTicketPayload rejects empty Title', function () {
        try {
            buildTicketPayload([
                'Sprint' => 'Sprint 10',
                'Change ID' => 'CH-UNIT-2',
                'Title' => '',
                'Change Owner' => 'Someone',
                'Change Type' => 'Normal',
                'Release Status' => 'Released',
            ], false);
            return false;
        } catch (TicketValidationException $e) {
            return str_contains($e->getMessage(), 'Title');
        }
    });
}
