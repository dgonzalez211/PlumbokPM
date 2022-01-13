<?php

use Doctrine\Common\Inflector\Inflector;
use PHPUnit\Framework\TestCase;


class ValueTest extends TestCase
{
	public function setUp(): void {
		\Octopush\Plumbok\Autoload::register('\\Octopush\\Plumbok\\Test');
	}

	public function testGettersGeneration(): void {
		$this->assertTrue(class_exists(Octopush\Plumbok\Test\Email::class));
		$reflection = new ReflectionClass(Octopush\Plumbok\Test\Email::class);

		foreach ($reflection->getProperties() as $property) {
			$getterExists = $reflection->hasMethod('get' . ucfirst($property->getName())) ||
				$reflection->hasMethod('is' . ucfirst(Inflector::singularize($property->getName())));
			$this->assertTrue($getterExists);

			$setterExists = $reflection->hasMethod('set' . ucfirst($property->getName()));
			$this->assertTrue($setterExists);
		}
	}
}
