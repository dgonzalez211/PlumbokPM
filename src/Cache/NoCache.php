<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Cache;

use Octopush\Plumbok\Cache;

/**
 * Class NoCache
 * @package Octopush\Plumbok\Cache
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class NoCache implements Cache
{
    /** @var array */
    private array $code = [];

    /**
     * Checks cached file freshness
     * @param string $className Source file name
     * @param int $time Source file modification time
     * @return bool
     */
    public function isFresh(string $className, int $time): bool {
        return false;
    }

    /**
     * @param string $className
     */
    public function load(string $className): void {
        if (array_key_exists($className, $this->code)) {
            eval($this->code[$className]);
        }
    }

    /**
     * Write file to cache
     * @param string $className
     * @param string $content
     */
    public function write(string $className, string $content): void {
        $this->code[$className] = $content;
    }
}
