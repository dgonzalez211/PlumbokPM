<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Generator;

use phpDocumentor\Reflection\Type;

/**
 * Class WithType
 * @package Octopush\Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
trait WithType
{
    /** @var Type */
    private Type $type;

    /**
     * Sets type
     * @param Type $type
     */
    public function setType(Type $type): void {
        $this->type = $type;
    }
}
