<?php

declare(strict_types=1);

namespace Brick\Translation\LocaleFallback;

use Brick\Translation\LocaleFallback;
use Brick\Translation\Translator;

class GlobalFallback implements LocaleFallback
{
    /**
     * @var string
     */
    private $fallbackLocale;

    /**
     * @param string $fallbackLocale
     */
    public function __construct(string $fallbackLocale)
    {
        $this->fallbackLocale = Translator::normalizeLocale($fallbackLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function getFallbackLocales(string $locale) : array
    {
        if ($locale === $this->fallbackLocale) {
            return [];
        }

        return [$this->fallbackLocale];
    }
}
