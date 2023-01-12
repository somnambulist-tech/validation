<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Exceptions;

use Exception;

class MessageException extends Exception
{
    public static function noMessageForKey(string $lang, string $key): self
    {
        return new self(sprintf('No message was found for the language "%s" and "%s"', $lang, $key));
    }

    public static function noMessageForKeys(string $lang, array $keys): self
    {
        return new self(sprintf('No message was found for the language "%s" and any of: "%s"', $lang, implode('", "', $keys)));
    }

    public static function messageFileDoesNotExist(string $file): self
    {
        return new self(sprintf('A message language file "%s" could not be found', $file));
    }

    public static function messageFileNotReadable(string $file): self
    {
        return new self(sprintf('A message language file "%s" exists, but could not be read', $file));
    }
}
