<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Generator;

use Octopush\Plumbok\Compiler\Code\Property;

/**
 * Class WithProperties
 * @package Octopush\Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
trait WithProperties
{
    /**
     * @var Property[]
     */
    private array $properties;

    /**
     * @param Property ...$properties
     */
    public function setProperties(Property ...$properties): void {
        $this->properties = $properties;
    }
}
