# Architecture Notes

This document explains the private Searchwiz-style product API at a reviewer-friendly level without exposing production source code, endpoint URLs, allowlist values, provider contracts, product exports, SKUs, catalog payloads, or operational logs.

## Operating Model

The plugin treats product feed traffic as read-heavy integration traffic. Instead of asking WooCommerce to rebuild product documents during every API request, it builds a snapshot table in the background and serves API responses from that table.

## Workflow

1. An authorized administrator installs and enables the plugin.
2. The plugin prepares a dedicated snapshot table when schema setup is required.
3. An operator starts a full rebuild from the admin console.
4. A cron tick processes products in small batches and writes normalized JSON documents to the snapshot table.
5. Product-list requests read paginated documents directly from the snapshot table.
6. Single-product requests read by product identifier or slug and return only when snapshot data is ready.
7. A delta refresh can later rebuild changed products based on modification timestamps.
8. A daily schedule can trigger a delta refresh when no rebuild is already running.
9. Operators can stop, run the next batch manually, or clear the snapshot through explicit admin actions.

## API Serving Notes

- Product-list responses are paginated and bounded by configurable default and maximum sizes.
- Single-product reads prefer snapshot data and avoid heavy live product formatting during ordinary API traffic.
- Short-lived response caching reduces repeated reads for identical page/size or product requests.
- Cache headers make integration behavior easier to reason about without exposing private response examples.

## Snapshot Builder Notes

- Batch size and time budget are treated as operational controls, not fixed assumptions.
- A short lock prevents concurrent ticks from competing for the same rebuild state.
- Full rebuild and delta refresh share the same state machine but use different product-selection criteria.
- The builder records progress, last processed ID, timestamps, and recent error context for operator visibility.
- Snapshot rows store normalized JSON rather than requiring repeated joins across product, taxonomy, image, and attribute tables.

## Access and Safety Decisions

- Admin users with the proper capability can inspect and control rebuild operations.
- Integration access is deliberately constrained; real allowlist values and provider details are private.
- Approved integration traffic is still rate-limited.
- Sensitive WordPress, login, cart, checkout, and mutation paths are not treated as product-feed access.
- Unauthorized or unsafe requests fail closed.

## Product Document Notes

The private formatter composes a search/feed-oriented document from product fields, link, media, availability, prices, category path, attributes, brand, and optional identifier metadata. This showcase does not publish the exact production response contract or real catalog examples.

## Privacy Notes

This showcase intentionally avoids real endpoints, IP addresses, provider rules, catalog payloads, product IDs, SKUs, image URLs, category trees, GTIN values, product exports, response samples, logs, and credentials. Phase 3 samples are simplified review snippets only.
