<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Command;

use Octopush\Plumbok\Cache\FileCache;
use Octopush\Plumbok\Cache\NoCache;
use Octopush\Plumbok\Compiler;
use Octopush\Plumbok\Compiler\NodeFinder;
use Octopush\Plumbok\TagsUpdater;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CompileCommand
 * @package Octopush\Plumbok\Command
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class CompileCommand extends Command
{
    /** @var Parser */
    private Parser $phpParser;
    /** @var NodeFinder */
    private NodeFinder $nodeFinder;

    public function configure(): void {
        $this->setName('compile');
        $this->setDescription('Compile files with annotations recursively from given path');
        $this->addArgument('path', InputArgument::REQUIRED, 'Search path for source files');
        $this->addArgument('cache', InputArgument::OPTIONAL, 'Cache path for compiled files');
        $this->addOption('no-tags', '', InputOption::VALUE_NONE, 'Do not push any @method tags to source files');
        $this->addOption('inline', 'i', InputOption::VALUE_NONE, 'Save generated code replacing source files');
        $this->addOption('ext', 'e', InputOption::VALUE_OPTIONAL, 'Additional source extension to search', 'php');
    }

    public function execute(InputInterface $input, OutputInterface $output): void {
        $output->getFormatter()->setStyle('error', new OutputFormatterStyle('red'));
        $inline = (bool)$input->getOption('inline');
        $noTags = (bool)$input->getOption('no-tags');
        $ext = $input->getOption('ext');
        $path = $input->getArgument('path');
        if (!file_exists($path)) {
            throw new RuntimeException("Path not exists, given: $path");
        }
        $cache = $input->getArgument('cache');
        if (empty($cache)) {
            if (!$inline) {
                throw new RuntimeException("Empty cache path with no --inline option, given: $cache");
            }
            $cache = new NoCache();
        } else {
            $cache = new FileCache($cache);
        }
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $files = new RegexIterator($files, "/\\.$ext$/");

        $compiler = new Compiler();
        $serializer = new Standard();
        $this->phpParser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->nodeFinder = new NodeFinder();

        /** @var SplFileInfo[] $files */
        foreach ($files as $file) {
            $filename = $file->getRealPath();
            $filepath = $file->getPathname();

            $output->writeln("[$filepath] <info>Processing file </info>", OutputInterface::VERBOSITY_VERBOSE);
            try {
                $className = $this->findClassName($filepath);
            } catch (RuntimeException $exception) {
                $output->writeln("[$filepath] <error>{$exception->getMessage()}</error>", OutputInterface::VERBOSITY_VERBOSE);
                continue;
            }
            $output->writeln("[$filepath] <info>Found class:</info> $className", OutputInterface::VERBOSITY_VERBOSE);
            if (!$inline && $cache->isFresh($className, filemtime($filepath))) {
                $output->writeln("[$filepath] <error>File is up to date omitting file.</error>", OutputInterface::VERBOSITY_VERBOSE);
            } else {
                if ($inline) {
                    $output->writeln("[$filepath] <info>Freshness check omitted.</info>", OutputInterface::VERBOSITY_VERBOSE);
                }
                $output->writeln("[$filepath] <info>Starting compilation...</info>", OutputInterface::VERBOSITY_VERBOSE);
                $nodes = $compiler->compile($filename);
                if (count($nodes)) {
                    if (!$noTags && !$inline) {
                        $tagsUpdater = new TagsUpdater(new NodeFinder());
                        $tagsUpdater->applyNodes($filename, ...$nodes);
                    }
                    if ($inline) {
                        file_put_contents($filename, $serializer->prettyPrintFile($nodes));
                    } else {
                        $cache->write($className, $serializer->prettyPrint($nodes));
                    }
                    $output->writeln("[$filepath] <info>Successfully compiled.</info>", OutputInterface::VERBOSITY_VERBOSE);
                } else {
                    $output->writeln("[$filepath] <error>No annotations, omitting file.</error>", OutputInterface::VERBOSITY_VERBOSE);
                }
            }
        }
    }

    /**
     * @param string $filepath
     * @return string
     */
    protected function findClassName(string $filepath): string {
        $className = '';
        $nodes = $this->phpParser->parse(file_get_contents($filepath));
        foreach ($this->nodeFinder->findNamespaces(...$nodes) as $namespace) {
            foreach ($this->nodeFinder->findClasses(...$namespace->stmts) as $class) {
                return $namespace->name . '\\' . $class->name->name;
            }
        }
        if (empty($className)) {
            foreach ($this->nodeFinder->findClasses(...$nodes) as $class) {
                return $class->name->name;
            }
        }

        throw new RuntimeException("Class not found in file");
    }
}
