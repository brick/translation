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
     * The locale fallback mechanism.
     *
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
     * @var string|null
     */
    private $defaultLocale;

    /**
     * @var string
     */
    private $parameterPrefix = '';

    /**
     * @var string
     */
    private $parameterSuffix = '';

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
     * @param string|null $locale
     *
     * @return void
     */
    public function setDefaultLocale(?string $locale) : void
    {
        $this->defaultLocale = $locale;
    }

    /**
     * @return string|null
     */
    public function getDefaultLocale() : ?string
    {
        return $this->defaultLocale;
    }

    /**
     * @param string $prefix
     * @param string $suffix
     *
     * @return void
     */
    public function setParameterPrefixSuffix(string $prefix, string $suffix) : void
    {
        $this->parameterPrefix = $prefix;
        $this->parameterSuffix = $suffix;
    }

    /**
     * @param string $key    The translation key to look up.
     * @param string $locale The locale to translate in.
     *
     * @return string|null The translated string, or null if not found.
     */
    private function rawTranslate(string $key, string $locale) : ?string
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
            $result = $this->rawTranslate($key, $fallbackLocale);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param string      $key     The translation key.
     * @param array       $params  The named parameters to replace in the translated text. Optional.
     * @param string|null $locale  The locale to translate to. Optional if a default locale has been set.
     * @param string|null $default The default text if no translation is available. Optional, defaults to the key.
     *
     * @return string
     *
     * @throws \RuntimeException If no locale is provided, and no default locale has been set.
     */
    public function translate(string $key, array $params = [], string $locale = null, string $default = null) : string
    {
        if ($locale === null) {
            if ($this->defaultLocale === null) {
                throw new \RuntimeException('No default locale has been set. A locale must be provided.');
            }

            $locale = $this->defaultLocale;
        }

        $text = $this->rawTranslate($key, $locale);

        if ($text === null) {
            $text = $default ?? $key;
        }

        if ($params) {
            $text = $this->replaceParameters($text, $params);
        }

        return $text;
    }

    /**
     * @param string      $key     The translation key.
     * @param array       $params  The named parameters to replace in the translated text. Optional.
     * @param string|null $locale  The locale to translate to. Optional if a default locale has been set.
     *
     * @return string|null The translated string, or null if not available.
     *
     * @throws \RuntimeException If no locale is provided, and no default locale has been set.
     */
    public function translateOrNull(string $key, array $params = [], string $locale = null) : ?string
    {
        if ($locale === null) {
            if ($this->defaultLocale === null) {
                throw new \RuntimeException('No default locale has been set. A locale must be provided.');
            }

            $locale = $this->defaultLocale;
        }

        $text = $this->rawTranslate($key, $locale);

        if ($text !== null && $params) {
            $text = $this->replaceParameters($text, $params);
        }

        return $text;
    }

    /**
     * Replaces parameters in a string.
     *
     * This is called internally by `translateReplace()`, but is exposed as a public method to allow
     * more advanced uses such as replacing parameters after executing a transformation on a
     * translated string.
     *
     * @param string $text
     * @param array  $parameters
     *
     * @return string
     */
    public function replaceParameters(string $text, array $parameters) : string
    {
        $placeholders = [];

        foreach ($parameters as $key => $value) {
            $key = $this->parameterPrefix . $key . $this->parameterSuffix;
            $placeholders[$key] = $value;
        }

        return strtr($text, $placeholders);
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
