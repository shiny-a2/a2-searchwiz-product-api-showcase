<?php

declare(strict_types=1);

function showcase_evaluate_feed_request(array $request, array $approvedClients): array
{
    $method = strtoupper(trim((string) ($request['method'] ?? 'GET')));
    $path = '/' . trim((string) ($request['path'] ?? ''), '/');
    $client = trim((string) ($request['client_fingerprint'] ?? ''));

    if (!in_array($method, ['GET', 'HEAD'], true)) {
        return showcase_policy_decision(false, 'Only read requests are allowed.');
    }

    if (showcase_is_blocked_commerce_path($path)) {
        return showcase_policy_decision(false, 'Commerce mutation paths are outside the feed boundary.');
    }

    if (!in_array($client, $approvedClients, true)) {
        return showcase_policy_decision(false, 'Client is not approved for feed access.');
    }

    if (!showcase_is_feed_path($path)) {
        return showcase_policy_decision(false, 'Request path is not part of the product feed API.');
    }

    return showcase_policy_decision(true, 'Request is inside the public product-feed boundary.');
}

function showcase_is_feed_path(string $path): bool
{
    return in_array($path, ['/feed/products', '/feed/product'], true);
}

function showcase_is_blocked_commerce_path(string $path): bool
{
    foreach (['/admin', '/login', '/cart', '/checkout', '/account'] as $blockedPrefix) {
        if ($path === $blockedPrefix || strpos($path, $blockedPrefix . '/') === 0) {
            return true;
        }
    }

    return false;
}

function showcase_policy_decision(bool $allowed, string $reason): array
{
    return [
        'allowed' => $allowed,
        'status' => $allowed ? 200 : 403,
        'reason' => $reason,
    ];
}
