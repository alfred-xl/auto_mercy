<?php

namespace App\Support;

class AttributionParameters
{
    /** @var list<string> */
    public const ALLOWED = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'gclid',
        'dclid',
        'gbraid',
        'wbraid',
        'fbclid',
        'msclkid',
    ];

    /** @param array<string, mixed> $query */
    public function from(array $query): array
    {
        return collect($query)
            ->only(self::ALLOWED)
            ->filter(fn (mixed $value): bool => is_scalar($value))
            ->map(fn (mixed $value): string => mb_substr(trim((string) $value), 0, 250))
            ->filter()
            ->all();
    }

    /** @param array<string, mixed> $query */
    public function hasUnsupported(array $query, array $additionalAllowed = []): bool
    {
        if (array_diff(array_keys($query), [...self::ALLOWED, ...$additionalAllowed]) !== []) {
            return true;
        }

        foreach (array_intersect_key($query, array_flip(self::ALLOWED)) as $value) {
            if (! is_scalar($value)) {
                return true;
            }

            $normalized = mb_substr(trim((string) $value), 0, 250);

            if ($normalized === '' || (string) $value !== $normalized) {
                return true;
            }
        }

        return false;
    }
}
