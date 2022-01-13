<?php
declare(strict_types=1);

namespace Octopush\Plumbok;

use Composer\Autoload\ClassLoader;
use Exception;
use InvalidArgumentException;
use PhpParser\PrettyPrinter\Standard;
use Octopush\Plumbok\Compiler\NodeFinder;
use RuntimeException;

/**
 * Class Autoload
 * @package Plumbok
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Autoload
{
    /** @var string */
    private string $namespace;
    /** @var int */
    private int $length;
    /** @var ClassLoader */
    private ClassLoader $classLoader;
    /** @var Compiler */
    private Compiler $compiler;
    /** @var Cache|null */
    private ?Cache $cache;
    /** @var Standard */
    private Standard $serializer;

    /**
     * @param string $namespace
     * @param Cache|null $cache Compiler cache, if null then cache in memory and eval code
     * @return bool
     */
    public static function register(string $namespace, Cache $cache = null): bool {
        if (empty($namespace)) {
            throw new InvalidArgumentException('Invalid namespace, trying to registered empty namespace');
        }
        foreach (spl_autoload_functions() as $loader) {
            if (is_array($loader) && is_a($loader[0], 'Symfony\\Component\\Debug\\DebugClassLoader')) {
                $loader = $loader[0]->getClassLoader();
            }

            if (false === is_array($loader)) {
                continue;
            }
            if (is_a($loader[0], ClassLoader::class) && method_exists($loader[0], 'findFile')) {
                $classLoader = $loader[0];
            }
        }
        if (isset($classLoader)) {
            $loader = new self($namespace, $classLoader, is_null($cache) ? new Cache\NoCache() : $cache);
            return spl_autoload_register([$loader, 'load'], true, true);
        }
        throw new RuntimeException("Unable to find Composer ClassLoader, did you forget require 'autoload.php'?");
    }

    /**
     * Autoload constructor.
     * @param string $namespace
     * @param ClassLoader $classLoader
     * @param Cache|null $cache
     */
    private function __construct(string $namespace, ClassLoader $classLoader, Cache $cache = null) {
        $this->namespace = $namespace;
        $this->length = strlen($namespace);
        $this->classLoader = $classLoader;
        $this->compiler = new Compiler();
        $this->serializer = new Standard();
        $this->cache = $cache;
    }

	/**
	 * @param string $class
	 * @return void
	 * @throws Exception
	 */
    public function load(string $class): void{
        if (substr($class, 0, $this->length) === $this->namespace) {
            $filename = $this->classLoader->findFile($class);
            if (file_exists($filename)) {
                if ($this->cache->isFresh($class, filemtime($filename))) {
                    $this->cache->load($class);
                } else {
                    $nodes = $this->compiler->compile($filename);
                    if (count($nodes)) {
                        $tagsUpdater = new TagsUpdater(new NodeFinder());
                        $tagsUpdater->applyNodes($filename, ...$nodes);
                        $this->cache->write($class, $this->serializer->prettyPrint($nodes));
                        $this->cache->load($class);
                    }
                }
            }
        }
    }
}
