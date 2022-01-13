<?php
declare(strict_types = 1);

namespace Octopush\Plumbok\Test\Day;

use Octopush\Plumbok\Annotation\RequiredArgsConstructor;

/**
 * @RequiredArgsConstructor
 * @method void __construct(int $day)
 */
class DayOfMonth
{
    /** @var int */
    private int $day;
    /** @var int */
    private int $month = 1;
}
