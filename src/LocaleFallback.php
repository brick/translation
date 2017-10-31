<?php

declare(strict_types=1);

namespace Brick\Translation;

interface LocaleFallback
{
    /**
     * Returns a list of fallback locales for the given locale.
     *
     * The method must return a list of locales to fallback to, if a text cannot be found in the given locale.
     * This list can be empty, and MUST NOT include the given locale.
     *
     * @param string $locale The locale, lowercase, dash-separated.
     *
     * @return string[]
     */
    public function getFallbackLocales(string $locale) : array;
}
