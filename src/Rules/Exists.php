<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Closure;
use Doctrine\DBAL\Connection;
use Somnambulist\Components\Validation\Rule;

class Exists extends Rule
{
    protected string $message = 'rule.exists';
    protected array $fillableParams = ['table', 'column'];

    public function __construct(private Connection $connection)
    {
    }

    public function table(string $table): self
    {
        $this->params['table'] = $table;

        return $this;
    }

    public function column(string $column): self
    {
        $this->params['column'] = $column;

        return $this;
    }

    public function where(Closure $callback): self
    {
        $this->params['callback'] = $callback;

        return $this;
    }

    public function check(mixed $value): bool
    {
        $this->assertHasRequiredParameters(['table', 'column']);

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('1')
            ->from($this->parameter('table'))
            ->where($qb->expr()->eq($this->parameter('column'), ':value'))
            ->setParameter('value', $value)
        ;

        if (null !== $func = $this->parameter('callback')) {
            $func($qb);
        }

        return 1 === (int)$qb->fetchOne();
    }
}
