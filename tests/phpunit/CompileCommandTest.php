<?php

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class CompileCommandTest extends \PHPUnit\Framework\TestCase
{
	public function setUp(): void {
		vfsStreamWrapper::register();
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('cache'));
	}

	protected function runCompileCommand(InputInterface $input): int {
		$app = new Application();
		$app->setAutoExit(false);
		$app->add(new \Octopush\Plumbok\Command\CompileCommand());

		return $app->run($input, new NullOutput());
	}

	public function testCompilation(): void {
		$src = realpath(__DIR__ . '/../classes');
		$cache = vfsStream::url('cache');
		$exitCode = $this->runCompileCommand(new StringInput("compile {$src} {$cache}"));
		$this->assertEquals(0, $exitCode);
		$exitCode = $this->runCompileCommand(new StringInput("compile {$src} {$cache}"));
		$this->assertEquals(0, $exitCode);
	}

	public function testCompilationWithoutCache(): void {
		$src = realpath(__DIR__ . '/../classes');
		$exitCode = $this->runCompileCommand(new StringInput("compile {$src}"));
		$this->assertEquals(1, $exitCode);
	}

	public function testCompilationFailOnNonexistantPath(): void {
		$src = realpath(__DIR__ . '/../nonexistant');
		$cache = vfsStream::url('cache');
		$exitCode = $this->runCompileCommand(new StringInput("compile {$src} {$cache}"));
		$this->assertEquals(1, $exitCode);
	}
}
