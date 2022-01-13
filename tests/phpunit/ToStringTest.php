<?php


use octopush\plumbok\Autoload;
use PHPUnit\Framework\TestCase;

class ToStringTest extends TestCase
{
	public function setUp(): void {
		Autoload::register('Octopush\\Plumbok\\Test');
	}

	public function testEqual(): void {
		$this->assertTrue(class_exists(Octopush\Plumbok\Test\Email::class));
		$reflection = new ReflectionClass(Octopush\Plumbok\Test\Email::class);

		$this->assertTrue($reflection->hasMethod('toString'));
	}
}
