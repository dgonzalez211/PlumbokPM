<?php
declare(strict_types = 1);

namespace Octopush\Plumbok\Test;


use Octopush\Plumbok\Annotation\Getter;

/**
 * Class Number
 *
 * @method int getValue()
 * @method void setValue(int $value)
 */
class Number
{
    /**
     * @var int
     * @Getter @Setter()
     */
    private int $value;
}