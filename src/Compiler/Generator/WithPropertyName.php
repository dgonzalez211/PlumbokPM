<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Generator;

/**
 * Class WithPropertyName
 * @package Octopush\Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
trait WithPropertyName
{
    /** @var string Holds property name */
    private string $propertyName;

    /**
     * Sets property name
     * @param string $propertyName
     */
    public function setPropertyName(string $propertyName): void {
        $this->propertyName = $propertyName;
    }
}
