<?php
declare(strict_types=1);

namespace Octopush\Plumbok;

/**
 * Interface Cache
 * @package Plumbok
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
interface Cache
{
    /**
     * Checks cached file freshness
     * @param string $className Source class name
     * @param int $time Source file modification time
     * @return bool
     */
    public function isFresh(string $className, int $time): bool;

    /**
     * @param string $className
     */
    public function load(string $className): void;

    /**
     * Write file to cache
     * @param string $className
     * @param string $content
     */
    public function write(string $className, string $content): void;
}
