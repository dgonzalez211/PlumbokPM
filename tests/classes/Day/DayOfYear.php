<?php
declare(strict_types = 1);

namespace Octopush\Plumbok\Test\Day;

use Octopush\Plumbok\Annotation\AllArgsConstructor;
use Octopush\Plumbok\Annotation\EqualTo;

/**
 * @AllArgsConstructor
 * @EqualTo
 * @method void __construct(int|null $day, int|null $year)
 * @method bool equalTo(object $other)
 */
class DayOfYear
{
    /** @var int */
    private int $day;
    /** @var int */
    private int $year = 2016;
}