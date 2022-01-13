<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Code;

use phpDocumentor\Reflection\Type;

/**
 * Class Property
 * @package Octopush\Plumbok\Compiler\Code
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Property
{
	/** @var string */
	private string $name;
	/** @var Type */
	private Type $type;
	/** @var boolean */
	private bool $hasDefaultValue;
	/** @var array */
	private array $annotations;
	/** @var string */
	private string $setter;

	/**
	 * Property constructor.
	 * @param string $name
	 * @param Type $type
	 * @param bool $hasDefaultValue
	 * @param string $setter
	 * @param array $annotations
	 */
	public function __construct(string $name, Type $type, bool $hasDefaultValue, string $setter, array $annotations) {
		$this->name = $name;
		$this->type = $type;
		$this->annotations = $annotations;
		$this->setter = $setter;
		$this->hasDefaultValue = $hasDefaultValue;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return Type
	 */
	public function getType(): Type {
		return $this->type;
	}

	/**
	 * @return array
	 */
	public function getAnnotations(): array {
		return $this->annotations;
	}

	/**
	 * @return string
	 */
	public function getSetter(): string {
		return $this->setter;
	}

	/**
	 * @return boolean
	 */
	public function hasDefaultValue(): bool {
		return $this->hasDefaultValue;
	}
}
