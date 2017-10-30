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
     * @param string $key    The translation key to look up.
     * @param string $locale The locale to translate in, or null to use the default locale.
     *
     * @return string|null The translated string, or null if not found.
     *
     * @throws \Exception
     */
    public function translate(string $key, string $locale) : ?string
    {
        if (! isset($this->dictionaries[$locale])) {
            $this->dictionaries[$locale] = $this->loader->load($locale);
        }

        return $this->dictionaries[$locale][$key] ?? null;
    }
}
