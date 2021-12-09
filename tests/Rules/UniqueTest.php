<?php declare(strict_types=1);

namespace Somnambulist\Components\Validation\Tests\Rules;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Rules\Unique;

/**
 * Class UniqueTest
 *
 * @package    Somnambulist\Components\Validation\Tests\Rules
 * @subpackage Somnambulist\Components\Validation\Tests\Rules\UniqueTest
 */
class UniqueTest extends TestCase
{
    public function testUnique()
    {
        $conn = DriverManager::getConnection(['url' => 'sqlite:///:memory:']);
        $conn->executeStatement('
            CREATE TABLE IF NOT EXISTS users (
                id char(36) NOT NULL,
                name varchar(255) NOT NULL,
                is_active integer(1) NOT NULL DEFAULT(0),
                email varchar(100) NOT NULL,
                password varchar(255) NOT NULL
            )
        ');
        $conn->executeStatement(<<<SQL
            INSERT INTO users
                (id, name, is_active, email, password)
            VALUES 
                ('a93059e2-7964-4378-9f2a-cce67169061f', 'foo', 1, 'foo@example.org', 'password'),
                ('b1fe85a8-a5f0-4c0a-a437-b63eb9bc779f', 'bar', 1, 'bar@example.org', 'password'),
                ('72965338-2ede-4f1c-b365-2cf717c2857d', 'baz', 1, 'baz@example.org', 'password'),
                ('18be8be0-39cc-4421-8e3e-7020441e3d48', 'delta', 1, 'delta@example.org', 'password'),
                ('245a7999-96a6-4523-94eb-0a09e24552e6', 'eep', 1, 'eep@example.org', 'password'),
                ('f4757b37-35eb-46db-b2b5-c9269e7d218c', 'alpha', 1, 'alpha@example.org', 'password')
        SQL);

        $rule = new Unique($conn);
        $rule->table('users')->column('email');

        $this->assertFalse($rule->check('foo@example.org'));
        $this->assertTrue($rule->check('foo@example.com'));
    }

    public function testUniqueCanIgnoreValues()
    {
        $conn = DriverManager::getConnection(['url' => 'sqlite:///:memory:']);
        $conn->executeStatement('
            CREATE TABLE IF NOT EXISTS users (
                id char(36) NOT NULL,
                name varchar(255) NOT NULL,
                is_active integer(1) NOT NULL DEFAULT(0),
                email varchar(100) NOT NULL,
                password varchar(255) NOT NULL
            )
        ');
        $conn->executeStatement(<<<SQL
            INSERT INTO users
                (id, name, is_active, email, password)
            VALUES 
                ('a93059e2-7964-4378-9f2a-cce67169061f', 'foo', 1, 'foo@example.org', 'password'),
                ('b1fe85a8-a5f0-4c0a-a437-b63eb9bc779f', 'bar', 1, 'bar@example.org', 'password'),
                ('72965338-2ede-4f1c-b365-2cf717c2857d', 'baz', 1, 'baz@example.org', 'password'),
                ('18be8be0-39cc-4421-8e3e-7020441e3d48', 'delta', 1, 'delta@example.org', 'password'),
                ('245a7999-96a6-4523-94eb-0a09e24552e6', 'eep', 1, 'eep@example.org', 'password'),
                ('f4757b37-35eb-46db-b2b5-c9269e7d218c', 'alpha', 1, 'alpha@example.org', 'password')
        SQL);

        $rule = new Unique($conn);
        $rule->table('users')->column('email')->ignore('foo@example.org');

        $this->assertTrue($rule->check('foo@example.org'));
        $this->assertFalse($rule->check('bar@example.org'));
    }

    public function testUniqueCanIgnoreValuesFromOtherColumn()
    {
        $conn = DriverManager::getConnection(['url' => 'sqlite:///:memory:']);
        $conn->executeStatement('
            CREATE TABLE IF NOT EXISTS users (
                id char(36) NOT NULL,
                name varchar(255) NOT NULL,
                is_active integer(1) NOT NULL DEFAULT(0),
                email varchar(100) NOT NULL,
                password varchar(255) NOT NULL
            )
        ');
        $conn->executeStatement(<<<SQL
            INSERT INTO users
                (id, name, is_active, email, password)
            VALUES 
                ('a93059e2-7964-4378-9f2a-cce67169061f', 'foo', 1, 'foo@example.org', 'password'),
                ('b1fe85a8-a5f0-4c0a-a437-b63eb9bc779f', 'bar', 1, 'bar@example.org', 'password'),
                ('72965338-2ede-4f1c-b365-2cf717c2857d', 'baz', 1, 'baz@example.org', 'password'),
                ('18be8be0-39cc-4421-8e3e-7020441e3d48', 'delta', 1, 'delta@example.org', 'password'),
                ('245a7999-96a6-4523-94eb-0a09e24552e6', 'eep', 1, 'eep@example.org', 'password'),
                ('f4757b37-35eb-46db-b2b5-c9269e7d218c', 'alpha', 1, 'alpha@example.org', 'password')
        SQL);

        $rule = new Unique($conn);
        $rule->table('users')->column('email')->ignore('a93059e2-7964-4378-9f2a-cce67169061f', 'id');

        $this->assertTrue($rule->check('foo@example.org'));
        $this->assertFalse($rule->check('bar@example.org'));
    }

    public function testUniqueAllowsModifyingQuery()
    {
        $conn = DriverManager::getConnection(['url' => 'sqlite:///:memory:']);
        $conn->executeStatement('
            CREATE TABLE IF NOT EXISTS users (
                id char(36) NOT NULL,
                name varchar(255) NOT NULL,
                is_active integer(1) NOT NULL DEFAULT(0),
                email varchar(100) NOT NULL,
                password varchar(255) NOT NULL
            )
        ');
        $conn->executeStatement(<<<SQL
            INSERT INTO users
                (id, name, is_active, email, password)
            VALUES 
                ('a93059e2-7964-4378-9f2a-cce67169061f', 'foo', 0, 'foo@example.org', 'password'),
                ('b1fe85a8-a5f0-4c0a-a437-b63eb9bc779f', 'bar', 1, 'bar@example.org', 'password'),
                ('72965338-2ede-4f1c-b365-2cf717c2857d', 'baz', 0, 'baz@example.org', 'password'),
                ('18be8be0-39cc-4421-8e3e-7020441e3d48', 'delta', 0, 'delta@example.org', 'password'),
                ('245a7999-96a6-4523-94eb-0a09e24552e6', 'eep', 1, 'eep@example.org', 'password'),
                ('f4757b37-35eb-46db-b2b5-c9269e7d218c', 'alpha', 1, 'alpha@example.org', 'password')
        SQL);

        $rule = new Unique($conn);
        $rule->table('users')->column('email')->where(fn (QueryBuilder $qb) => $qb->andWhere('is_active = 1'));

        $this->assertTrue($rule->check('foo@example.org'));
        $this->assertTrue($rule->check('foo@example.com'));
        $this->assertFalse($rule->check('bar@example.org'));
    }
}
