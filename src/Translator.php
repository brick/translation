<?php

declare(strict_types=1);

namespace Brick\Translation;

use Brick\Translation\LocaleFallback\NullFallback;

/**
 * Base implementation of a translator.
 */
class Translator
{
    /**
     * The translation loader.
     *
     * @var TranslationLoader
     */
    private $loader;

    /**
     * @var LocaleFallback
     */
    private $localeFallback;

    /**
     * An associative array of locale to dictionary.
     *
     * Each dictionary is itself an associative array of keys to texts.
     *
     * @var array
     */
    private $dictionaries = [];

    /**
     * @param TranslationLoader   $loader         The translation loader.
     * @param LocaleFallback|null $localeFallback An optional locale fallback mechanism.
     */
    public function __construct(TranslationLoader $loader, LocaleFallback $localeFallback = null)
    {
        if ($localeFallback === null) {
            $localeFallback = new NullFallback();
        }

        $this->loader         = $loader;
        $this->localeFallback = $localeFallback;
    }

    /**
     * @param string $key    The translation key to look up.
     * @param string $locale The locale to translate in.
     *
     * @return string|null The translated string, or null if not found.
     *
     * @throws \Exception
     */
    public function translate(string $key, string $locale) : ?string
    {
        $locale = self::normalizeLocale($locale);

        if (! isset($this->dictionaries[$locale])) {
            $this->dictionaries[$locale] = $this->loader->load($locale);
        }

        if (isset($this->dictionaries[$locale][$key])) {
            return $this->dictionaries[$locale][$key];
        }

        $fallbackLocales = $this->localeFallback->getFallbackLocales($locale);

        foreach ($fallbackLocales as $fallbackLocale) {
            $result = $this->translate($key, $fallbackLocale);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    public static function normalizeLocale(string $locale) : string
    {
        return strtolower(str_replace('_', '-', $locale));
    }
}
