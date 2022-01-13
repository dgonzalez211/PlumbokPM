<?php

namespace Octopush\Plumbok\Test;

use octopush\plumbok\Annotation\Data;
use octopush\plumbok\Annotation\Getter;
use octopush\plumbok\Annotation\Setter;
use Octopush\Plumbok\Test\Day\DayOfMonth;


/**
 * @Data()
 * @method void __construct(array|null $names, null|int $age, \Octopush\Plumbok\Test\DayOfMonth|null $nameDay, int[]|null $favouriteNumbers)
 * @method array getNames()
 * @method void setNames(array $names)
 * @method null|int getAge()
 * @method void setAge(null|int $age)
 * @method \Octopush\Plumbok\Test\DayOfMonth getNameDay()
 * @method void setNameDay(\Octopush\Plumbok\Test\DayOfMonth $nameDay)
 * @method int[] getFavouriteNumbers()
 * @method void setFavouriteNumbers(int[] $favouriteNumbers)
 */
class Person
{
    /**
     * @var array
     * @Getter @Setter
     */
    private array $names = [];

    /**
     * Holds age
     * @var null|int
     * @Getter @Setter
     */
    private ?int $age;

    /**
     * @var DayOfMonth
     * @Getter @Setter
     */
    private DayOfMonth $nameDay;

    /**
     * @var int[]
     * @Getter @Setter
     */
    private array $favouriteNumbers = [1, 7, 14, 21, 28];
}
