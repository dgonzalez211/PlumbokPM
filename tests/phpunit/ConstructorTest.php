<?php


use PHPUnit\Framework\TestCase;

class ConstructorTest extends TestCase
{
	public function setUp(): void {
		\Octopush\Plumbok\Autoload::register('\\Octopush\\Plumbok\\Test');
	}

	public function testNoArgsConstructor(): void {
		$this->assertTrue(class_exists(Octopush\Plumbok\Test\Day\DayOfWeek::class));
		$reflection = new ReflectionClass(\Octopush\Plumbok\Test\Day\DayOfWeek::class);
		$this->assertTrue($reflection->hasMethod('__construct'));
		$this->assertEquals(0, $reflection->getMethod('__construct')->getNumberOfParameters());
	}

	public function testRequiredArgsConstructor(): void {
		$this->assertTrue(class_exists(Octopush\Plumbok\Test\Day\DayOfMonth::class));
		$reflection = new ReflectionClass(Octopush\Plumbok\Test\Day\DayOfMonth::class);

		$this->assertTrue($reflection->hasMethod('__construct'));
		$this->assertEquals(1, $reflection->getMethod('__construct')->getNumberOfParameters());
	}

	public function testAllArgsConstructor(): void {
		$this->assertTrue(class_exists(Octopush\Plumbok\Test\Day\DayOfYear::class));
		$reflection = new ReflectionClass(Octopush\Plumbok\Test\Day\DayOfYear::class);

		$this->assertTrue($reflection->hasMethod('__construct'));
		$this->assertEquals(2, $reflection->getMethod('__construct')->getNumberOfParameters());
	}
}
