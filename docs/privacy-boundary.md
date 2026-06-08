# Privacy Boundary

This public repository is designed for employer review and portfolio proof only.

## Public

- General snapshot API architecture.
- Public-safe notes on rebuilds, caching, pagination, access boundaries, and performance.
- Sanitized samples that demonstrate patterns without exposing production contracts.
- Reviewer-friendly discussion of operational tradeoffs, risks, and safeguards.

## Private

- Production source code.
- Endpoint URLs, access lists, provider-specific rules, product exports, logs, and credentials.
- Live catalog response shapes used by private consumers.
- Product IDs, SKUs, GTIN values, image URLs, category structures, pricing data, stock details, and customer/order data.
- Internal table names, option names, action hooks, nonce names, and operational identifiers where they would reveal implementation details.

## Rule

Public samples may explain the pattern, but never expose live access rules, real catalog data, provider contracts, or private product-feed response examples.
