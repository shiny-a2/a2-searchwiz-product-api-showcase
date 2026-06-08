# A2 Searchwiz Product API Showcase

Public-safe showcase for a private WooCommerce snapshot product API built for search/product-feed consumers that need fast catalog reads without expensive live WooCommerce queries.

This repository is documentation and sanitized portfolio proof only. It is not a source mirror. Production source, endpoint URLs, allowlist values, provider-specific contracts, product exports, live catalog payloads, and operational logs stay private.

## Review Summary

- **Problem:** search and feed consumers need predictable product data, but live WooCommerce queries can be slow, fragile, and unsafe under crawl or integration traffic.
- **Solution:** a snapshot-backed API that separates product-data generation from response serving, supports full and delta rebuilds, and protects integration access with explicit boundaries.
- **Engineering focus:** REST response design, snapshot-table strategy, batch rebuild jobs, cache headers, rate limiting, capability-gated admin controls, and privacy-safe integration boundaries.
- **Public scope:** architecture, operational notes, privacy boundary, and future sanitized snippets.

## Business Context

Commerce search integrations often read thousands of products repeatedly. Building each response directly from WooCommerce product objects, attributes, images, categories, and stock state can overload shared hosting or slow down storefront operations.

The private plugin is designed around a safer data pipeline:

1. Build product snapshots in controlled batches.
2. Store normalized catalog documents in a lightweight lookup table.
3. Serve paginated API responses from the snapshot instead of live product joins.
4. Support single-product reads by identifier or slug when snapshot data is ready.
5. Allow controlled full rebuilds and smaller delta refreshes.
6. Cache API responses briefly to reduce repeated read pressure.
7. Keep access limited to approved integration traffic and privileged admins.

## What This Demonstrates

- Snapshot-backed product API design for expensive WooCommerce data.
- Separation between snapshot generation, REST serving, and admin operations.
- Full rebuild and delta refresh workflow thinking.
- Batch processing with lock/time-budget safeguards.
- Response pagination, single-product lookup, and short-lived response caching.
- Integration boundary design without publishing real allowlist values or provider details.
- Operational controls for rebuild, run-next-batch, stop, and clear-snapshot workflows.
- Public-safe documentation of performance, privacy, and reliability tradeoffs.

## Architecture Overview

The private implementation is organized as a WordPress/WooCommerce plugin with a dedicated REST namespace and an admin snapshot console:

- **REST layer:** exposes product-list and single-product reads backed by the snapshot table.
- **Permission layer:** allows privileged administrators and approved integration traffic while rejecting unauthorized requests.
- **Rate limiter:** applies per-minute limits even for approved integration traffic.
- **Snapshot table:** stores one normalized JSON document per product with modified/update timestamps.
- **Batch builder:** processes products in small cron ticks to avoid request timeouts and memory spikes.
- **Delta refresh:** uses product modification timestamps to rebuild only changed products after the first successful run.
- **Formatter:** converts WooCommerce product details into a compact feed/search document.
- **Admin console:** gives operators controlled actions for full rebuild, delta refresh, stop, run-next-batch, and clear snapshot.
- **Caching:** uses short-lived transients and response headers to reduce repeated API work.

See `docs/architecture-notes.md` for the detailed reviewer walkthrough.

## Key Engineering Decisions

- **Snapshot first:** API traffic reads prebuilt documents, not live WooCommerce product objects.
- **Admin-safe operations:** rebuild controls are capability-gated and nonce-protected.
- **Batch over bulk:** snapshot generation runs in small batches to protect shared hosting stability.
- **Delta after baseline:** daily refresh can update changed products without rebuilding the entire catalog.
- **Fail closed:** unauthorized traffic receives a denied response instead of a partial feed.
- **No public contract leakage:** response examples and provider-specific access details are intentionally omitted.

## Tech Stack

- WordPress plugin architecture
- WooCommerce product data
- PHP
- WordPress REST API
- Snapshot/rebuild pattern
- Cron-based batch processing
- Transient caching and operational state
- Integration access controls

## Privacy Boundary

Public files describe the architecture and engineering approach only. Production source, endpoint URLs, access lists, product IDs, SKUs, catalog exports, provider contracts, live response payloads, logs, and credentials remain private.

Read the full boundary in `docs/privacy-boundary.md`.

## Reviewer Path

- Start with this README for the business case and implementation shape.
- Read `docs/architecture-notes.md` for workflow and module responsibilities.
- Read `docs/privacy-boundary.md` for what is intentionally excluded.
- Check `docs/update-notes.md` for public-facing change history.
- Review `samples/README.md` for the Phase 3 sanitized sample plan.

## Repository Structure

- `docs/architecture-notes.md` — architecture, workflow, and engineering decisions.
- `docs/privacy-boundary.md` — what is public versus private.
- `docs/update-notes.md` — public update log.
- `samples/README.md` — sanitized sample-code overview.
- `samples/php/` — planned short public-safe PHP snippets for snapshot DTOs, request boundaries, and batch state.

## Phase Status

- **Phase 1:** showcase skeleton, privacy boundary, and reviewer path.
- **Phase 2:** employer-friendly business context, architecture notes, and risk boundaries.
- **Phase 3:** planned sanitized snippets for product snapshot shaping, request access policy, and batch rebuild state.

## Links

- Portfolio: <https://amiraliyaghouti.com>
- GitHub profile: <https://github.com/shiny-a2>
- Private source: `shiny-a2/a2-searchwiz-product-api` (not public)
