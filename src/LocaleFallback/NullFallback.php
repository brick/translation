<?php

declare(strict_types=1);

namespace Brick\Translation\LocaleFallback;

use Brick\Translation\LocaleFallback;

class NullFallback implements LocaleFallback
{
    /**
     * {@inheritdoc}
     */
    public function getFallbackLocales(string $locale) : array
    {
        return [];
    }
}
