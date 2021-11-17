<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Behaviours;

/**
 * Trait MessagesTrait
 *
 * @package    Somnambulist\Components\Validation\Behaviours
 * @subpackage Somnambulist\Components\Validation\Behaviours\MessagesTrait
 */
trait MessagesTrait
{
    private array $messages = [];

    public function setMessage(string $key, string $message): void
    {
        $this->messages[$key] = $message;
    }

    public function getMessage(string $key): string
    {
        return array_key_exists($key, $this->messages) ? $this->messages[$key] : $key;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): void
    {
        $this->messages = array_merge($this->messages, $messages);
    }
}
