<?php

namespace Octopush\Plumbok\Test;

use Octopush\Plumbok\Annotation\Setter;
use Octopush\Plumbok\Annotation\ToString;
use Octopush\Plumbok\Annotation\Value;

/**
 * @Value()
 * @ToString(property="email")
 * @method void __construct(string|null $email, \Octopush\Plumbok\Test\UnannotatedClass|null $someObject)
 * @method bool equalTo(object $other)
 * @method string getEmail()
 * @method string toString()
 * @method \Octopush\Plumbok\Test\UnannotatedClass getSomeObject()
 * @method void setSomeObject(\Octopush\Plumbok\Test\UnannotatedClass $someObject)
 */
class Email
{
	/**
	 * @var string
	 */
	private string $email = '';

	/**
	 * @var UnannotatedClass
	 * @Setter @Getter
	 */
	private $someObject;

	private function setEmail(string $email)
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new \InvalidArgumentException("Email address is invalid, given: {$email}");
		}
		$this->email = $email;
	}
}
