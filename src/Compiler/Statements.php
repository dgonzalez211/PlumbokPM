<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use PhpParser\Node\Stmt;
use Traversable;

/**
 * Class Statements
 * @package Octopush\Plumbok\Compiler
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Statements implements IteratorAggregate, Countable
{
    /** @var Stmt[] Holds generated statements */
    private array $statements = [];

	/**
	 * Adds statement
	 * @param Stmt ...$stmts
	 */
    public function add(Stmt ...$stmts): void {
        foreach ($stmts as $stmt) {
            $this->statements[] = $stmt;
        }
    }

    /**
     * @param Statements $other
     * @return Statements
     */
    public function merge(Statements $other): Statements {
        $this->add(...$other->statements);

        return $this;
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable {
        return new ArrayIterator($this->statements);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count(): int {
        return count($this->statements);
    }
}
