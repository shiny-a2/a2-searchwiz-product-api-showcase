<?php

declare(strict_types=1);

function showcase_create_snapshot_job(string $mode, int $estimatedTotal, string $deltaSince = ''): array
{
    return [
        'status' => 'running',
        'mode' => in_array($mode, ['full', 'delta'], true) ? $mode : 'full',
        'estimated_total' => max(0, $estimatedTotal),
        'processed' => 0,
        'last_product_id' => 0,
        'delta_since' => $deltaSince,
        'last_error' => '',
    ];
}

function showcase_run_snapshot_batch(array $job, array $productRows, callable $writeSnapshot, int $batchSize = 100): array
{
    if (($job['status'] ?? '') !== 'running') {
        return $job;
    }

    $limit = max(1, $batchSize);
    $processedInBatch = 0;

    foreach ($productRows as $row) {
        if ($processedInBatch >= $limit) {
            break;
        }

        if (!showcase_should_snapshot_row($job, $row)) {
            continue;
        }

        $document = [
            'id' => (int) $row['id'],
            'modified_at' => (string) ($row['modified_at'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
        ];

        $writeSnapshot($document);

        $job['last_product_id'] = (int) $row['id'];
        $job['processed']++;
        $processedInBatch++;
    }

    if ($processedInBatch === 0 || $job['processed'] >= $job['estimated_total']) {
        $job['status'] = 'idle';
    }

    return $job;
}

function showcase_should_snapshot_row(array $job, array $row): bool
{
    $productId = (int) ($row['id'] ?? 0);

    if ($productId <= (int) ($job['last_product_id'] ?? 0)) {
        return false;
    }

    if (($job['mode'] ?? 'full') !== 'delta') {
        return true;
    }

    $modifiedAt = (string) ($row['modified_at'] ?? '');
    $deltaSince = (string) ($job['delta_since'] ?? '');

    return $deltaSince === '' || strcmp($modifiedAt, $deltaSince) > 0;
}
