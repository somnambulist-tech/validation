<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Contracts;

/**
 * Class MimeTypeGuesser
 *
 * @package    Somnambulist\Components\Validation
 * @subpackage Somnambulist\Components\Validation\MimeTypeGuesser
 */
interface MimeTypeGuesser
{
    public function getExtension(string $mimeType): ?string;

    public function getMimeType(string $extension): string;
}
