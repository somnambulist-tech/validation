<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Contracts;

interface MimeTypeGuesser
{
    public function getExtension(string $mimeType): ?string;

    public function getMimeType(string $extension): string;
}
