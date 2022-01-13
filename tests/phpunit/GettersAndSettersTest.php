<?php

use Doctrine\Common\Inflector\Inflector;
use Octopush\Plumbok\Autoload;
use Octopush\Plumbok\Test\Number;
use Octopush\Plumbok\Test\Person;
use PHPUnit\Framework\TestCase;


class GettersAndSettersTest extends TestCase
{
	public function setUp(): void {
		Autoload::register('\\Octopush\\Plumbok\\Test');
	}

	public function testGettersGeneration(): void {
		$this->assertTrue(class_exists(Person::class), 'Autoloading failed');
		$reflection = new ReflectionClass(Person::class);

		foreach ($reflection->getProperties() as $property) {
			$getterExists = $reflection->hasMethod('get' . ucfirst($property->getName())) ||
				$reflection->hasMethod('is' . ucfirst(Inflector::singularize($property->getName())));

			$this->assertTrue($getterExists);
		}
	}

	public function testSettersGeneration(): void {
		$this->assertTrue(class_exists(Person::class));
		$reflection = new ReflectionClass(Person::class);

		foreach ($reflection->getProperties() as $property) {
			$setterExists = $reflection->hasMethod($setter = 'set' . ucfirst($property->getName()));
			$this->assertTrue($setterExists);
		}
	}

	public function testGetterOnNonAnnotatedClass(): void {
		$this->assertTrue(class_exists(Number::class));
		$reflection = new ReflectionClass(Number::class);

		$this->assertTrue($reflection->hasMethod('getValue'));
	}
}
