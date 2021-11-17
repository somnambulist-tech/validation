<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Behaviours;

/**
 * Trait TranslationsTrait
 *
 * @package    Somnambulist\Components\Validation\Behaviours
 * @subpackage Somnambulist\Components\Validation\Behaviours\TranslationsTrait
 */
trait TranslationsTrait
{
    private array $translations = [];

    public function setTranslation(string $key, string $translation): void
    {
        $this->translations[$key] = $translation;
    }

    public function getTranslation(string $key): string
    {
        return array_key_exists($key, $this->translations) ? $this->translations[$key] : $key;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function setTranslations(array $translations): void
    {
        $this->translations = array_merge($this->translations, $translations);
    }
}
