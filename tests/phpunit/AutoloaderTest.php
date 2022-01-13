<?php

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class AutoloaderTest extends \PHPUnit\Framework\TestCase
{
	public function setUp(): void {
		vfsStreamWrapper::register();
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('src'));
	}

	public function testRegisterWithoutNamespace(): void {
		$this->expectException(InvalidArgumentException::class);
		\Octopush\Plumbok\Autoload::register('');
	}

	public function testRegister(): void {
		$count = count(spl_autoload_functions());
		\Octopush\Plumbok\Autoload::register('\\Octopush\\Plumbok\\Test');
		$this->assertGreaterThan($count, count(spl_autoload_functions()));
	}

	public function testRegisterWithCache(): void {
		\Octopush\Plumbok\Autoload::register('Octopush\\Plumbok\\Test', new \Octopush\Plumbok\Cache\FileCache(vfsStream::url('src/cache')));
		$this->assertTrue(class_exists(Octopush\Plumbok\Test\Day\DayOfWeek::class));
		$this->assertTrue(class_exists(Octopush\Plumbok\Test\Day\DayOfWeek::class, false));
		$this->assertFileExists(vfsStream::url('src/cache/Octopush.Plumbok.Test.Day.DayOfWeek.php'));
	}

	public function testLoad(): void {
		\octopush\plumbok\Autoload::register('\\Octopush\\Plumbok\\Test');

		$this->assertTrue(class_exists(Octopush\Plumbok\Test\Day\DayOfWeek::class));
		$this->assertTrue(class_exists(Octopush\Plumbok\Test\Day\DayOfWeek::class, false));
	}


}
