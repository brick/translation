<?php

declare(strict_types=1);

namespace Brick\Translation\LocaleFallback;

use Brick\Translation\LocaleFallback;

/**
 * Recursively truncates a locale according to RFC 4646, section 4.3.2. Truncation of Language Tags.
 *
 * @see https://tools.ietf.org/html/rfc4646
 */
class TruncateFallback implements LocaleFallback
{
    /**
     * {@inheritdoc}
     */
    public function getFallbackLocales(string $locale) : array
    {
        $result = [];

        for (;;) {
            $truncated = preg_replace('/^(.*?)(?:\-[^\-])?\-[^\-]+$/', '$1', $locale);

            if ($truncated === $locale) {
                break;
            }

            $result[] = $locale = $truncated;
        }

        return $result;
    }
}
