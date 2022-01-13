<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Generator;

/**
 * Class WithClassName
 * @package Octopush\Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
trait WithClassName
{
    /** @var string Holds class name */
    private string $className;

    /**
     * @param string $className
     */
    public function setClassName(string $className): void {
        $this->className = $className;
    }
}
