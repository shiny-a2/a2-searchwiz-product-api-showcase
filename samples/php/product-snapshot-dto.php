<?php

declare(strict_types=1);

function showcase_build_product_snapshot(array $product): array
{
    return [
        'id' => showcase_product_identifier($product['id'] ?? ''),
        'title' => showcase_clean_text($product['title'] ?? ''),
        'url' => showcase_public_url($product['url'] ?? ''),
        'availability' => showcase_availability_label($product['available'] ?? false),
        'category_path' => showcase_category_path($product['categories'] ?? []),
        'attributes' => showcase_public_attributes($product['attributes'] ?? []),
    ];
}

function showcase_product_identifier($value): string
{
    $identifier = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $value);

    return $identifier !== '' ? $identifier : 'sample-product';
}

function showcase_clean_text($value): string
{
    return trim(strip_tags(html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8')));
}

function showcase_public_url($value): string
{
    $url = filter_var((string) $value, FILTER_VALIDATE_URL);

    return $url !== false ? $url : 'https://example.com/products/sample-product';
}

function showcase_availability_label($available): string
{
    return filter_var($available, FILTER_VALIDATE_BOOLEAN) ? 'in_stock' : 'out_of_stock';
}

function showcase_category_path(array $categories): string
{
    $clean = array_filter(array_map('showcase_clean_text', $categories));

    return implode(' > ', $clean);
}

function showcase_public_attributes(array $attributes): array
{
    $publicAttributes = [];

    foreach ($attributes as $name => $value) {
        $cleanName = showcase_clean_text($name);
        $cleanValue = showcase_clean_text($value);

        if ($cleanName !== '' && $cleanValue !== '') {
            $publicAttributes[$cleanName] = $cleanValue;
        }
    }

    return $publicAttributes;
}
