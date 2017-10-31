<?php

declare(strict_types=1);

namespace Brick\Translation\LocaleFallback;

use Brick\Translation\LocaleFallback;
use Brick\Translation\Translator;

class ConfigurableFallback implements LocaleFallback
{
    /**
     * @var array
     */
    private $fallbacks = [];

    /**
     * {@inheritdoc}
     */
    public function getFallbackLocales(string $locale) : array
    {
        $result = [];

        while (isset($this->fallbacks[$locale])) {
            $result[] = $locale = $this->fallbacks[$locale];
        }

        return $result;
    }

    /**
     * @param string $locale
     * @param string $fallbackLocale
     *
     * @return void
     */
    public function addFallback(string $locale, string $fallbackLocale) : void
    {
        $locale         = Translator::normalizeLocale($locale);
        $fallbackLocale = Translator::normalizeLocale($fallbackLocale);

        $this->fallbacks[$locale] = $fallbackLocale;
    }

    /**
     * @param array $fallbacks
     *
     * @return void
     */
    public function addFallbacks(array $fallbacks) : void
    {
        foreach ($fallbacks as $locale => $fallbackLocale) {
            $this->addFallback($locale, $fallbackLocale);
        }
    }
}
