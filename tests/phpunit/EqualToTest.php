<?php


use PHPUnit\Framework\TestCase;

class EqualToTest extends TestCase
{
	public function setUp(): void {
		\Octopush\Plumbok\Autoload::register('\\Octopush\\Plumbok\\Test');
	}

	public function testEqualTo(): void {
		$this->assertTrue(class_exists(Octopush\Plumbok\Test\Day\DayOfYear::class));
		$reflection = new ReflectionClass(Octopush\Plumbok\Test\Day\DayOfYear::class);

		$this->assertTrue($reflection->hasMethod('equalTo'));
	}
}
