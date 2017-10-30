<?php

declare(strict_types=1);

namespace Brick\Translation;

/**
 * Base implementation of a translator.
 */
class Translator
{
    /**
     * The translation loader.
     *
     * @var \Brick\Translation\TranslationLoader
     */
    private $loader;

    /**
     * The locale to use if none is given in `translate()`.
     *
     * @var string|null
     */
    private $defaultLocale = null;

    /**
     * An associative array of locale to dictionary.
     *
     * Each dictionary is itself an associative array of keys to texts.
     *
     * @var array
     */
    private $dictionaries = [];

    /**
     * @param TranslationLoader $loader
     */
    public function __construct(TranslationLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param string|null $defaultLocale The new default locale, or null to remove it.
     *
     * @return void
     */
    public function setDefaultLocale(?string $defaultLocale) : void
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @return string|null
     */
    public function getDefaultLocale() : ?string
    {
        return $this->defaultLocale;
    }

    /**
     * @param string      $key    The translation key to look up.
     * @param string|null $locale The locale to translate in, or null to use the default locale.
     *
     * @return string|null The translated string, or null if not found.
     *
     * @throws \Exception
     */
    public function translate(string $key, string $locale = null) : ?string
    {
        if ($locale === null) {
            if ($this->defaultLocale === null) {
                throw new \Exception('No default locale has been set.');
            }

            $locale = $this->defaultLocale;
        }

        if (! isset($this->dictionaries[$locale])) {
            $this->dictionaries[$locale] = $this->loader->load($locale);
        }

        $dictionary = $this->dictionaries[$locale];

        return $dictionary[$key] ?? null;
    }
}
