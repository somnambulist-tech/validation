<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Rules;

use Closure;
use Doctrine\DBAL\Connection;
use Somnambulist\Components\Validation\Rule;

class Unique extends Rule
{
    protected string $message = 'rule.unique';
    protected array $fillableParams = ['table', 'column', 'ignore', 'ignore_column'];

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

    public function ignore(mixed $value, string $column = null): self
    {
        $this->params['ignore']        = $value;
        $this->params['ignore_column'] = $column;

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
            ->select('COUNT(*) AS cnt')
            ->from($this->parameter('table'))
            ->where($qb->expr()->eq($this->parameter('column'), ':value'))
            ->setParameter('value', $value)
        ;

        if ($this->parameter('ignore')) {
            $qb
                ->andWhere($qb->expr()->neq($this->parameter('ignore_column') ?? $this->parameter('column'), ':ignore'))
                ->setParameter('ignore', $this->parameter('ignore'))
            ;
        }

        if (null !== $func = $this->parameter('callback')) {
            $func($qb);
        }

        return 0 === (int)$qb->executeQuery()->fetchOne();
    }
}
