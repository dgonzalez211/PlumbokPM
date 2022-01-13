<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler;

use Octopush\Plumbok\Annotation\AllArgsConstructor;
use Octopush\Plumbok\Annotation\Data;
use Octopush\Plumbok\Annotation\EqualTo;
use Octopush\Plumbok\Annotation\NoArgsConstructor;
use Octopush\Plumbok\Annotation\RequiredArgsConstructor;
use Octopush\Plumbok\Annotation\ToString;
use Octopush\Plumbok\Annotation\Value;
use Octopush\Plumbok\Compiler\Code\Property;
use UnexpectedValueException;

/**
 * Class Context
 * @package Octopush\Plumbok\Compiler
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Context
{
    /**
     * @var array Holds applied annotations
     */
    private array $applied = [];
    /**
     * @var array Holds self excluding annotations
     */
    private static array $excludingAnnotations = [
        'generic' => [
            Value::class,
            Data::class,
        ],
        'constructor' => [
            AllArgsConstructor::class,
            NoArgsConstructor::class,
            RequiredArgsConstructor::class,
        ],
    ];
    /**
     * @var bool Holds all argument constructor creation flag
     */
    private bool $allArgsConstructor = false;
    /**
     * @var bool Holds no argument constructor creation flag
     */
    private bool $noArgsConstructor = false;
    /**
     * @var bool Holds only required arguments constructor creation flag
     */
    private bool $requiredArgsConstructor = false;
    /**
     * @var bool Holds all properties getter creation flag
     */
    private bool $allPropertyGetters = false;
    /**
     * @var bool Holds all properties setter creation flag
     */
    private bool $allPropertySetters = false;
    /**
     * @var bool Holds equality comparator creation flag
     */
    private bool $equalTo = false;
    /**
     * @var string Holds property returned by toString()
     */
    private string $toString;

    /**
     * Context constructor.
     * @param array $annotations
     */
    public function __construct(array $annotations) {
        foreach ($annotations as $annotation) {
            switch (get_class($annotation)) {
                case Value::class:
                case Data::class:
                case AllArgsConstructor::class:
                case NoArgsConstructor::class:
                case RequiredArgsConstructor::class:
                case EqualTo::class:
                case ToString::class:
                    if ($this->checkNonExcludingUsage($annotation)) {
                        $this->apply($annotation);
                    }
                    break;
            }
        }
    }

    private function applyValue(Value $annotation): void {
        $this->allArgsConstructor = true;
        $this->allPropertyGetters = true;
        $this->equalTo = true;
    }

    private function applyData(Data $annotation): void {
        $this->allArgsConstructor = true;
        $this->allPropertyGetters = true;
        $this->allPropertySetters = true;
    }

    private function applyAllArgsConstructor(AllArgsConstructor $annotation): void {
        $this->allArgsConstructor = true;
        $this->noArgsConstructor = false;
        $this->requiredArgsConstructor = false;
    }

    private function applyNoArgsConstructor(NoArgsConstructor $annotation): void {
        $this->noArgsConstructor = true;
        $this->allArgsConstructor = false;
        $this->requiredArgsConstructor = false;
    }

    private function applyRequiredArgsConstructor(RequiredArgsConstructor $annotation): void {
        $this->requiredArgsConstructor = true;
        $this->allArgsConstructor = false;
        $this->noArgsConstructor = false;
    }

    private function applyEqualTo(EqualTo $annotation): void {
        $this->equalTo = true;
    }

    private function applyToString(ToString $annotation): void {
        $this->toString = $annotation->property;
    }

	/**
	 * @param mixed $annotation
	 * @uses applyValue
	 * @uses applyData
	 * @uses applyAllArgsConstructor
	 * @uses applyNoArgsConstructor
	 * @uses applyRequiredArgsConstructor
	 * @uses applyEqualTo
	 * @uses applyToString
	 */
    private function apply(mixed $annotation): void {
        $name = str_replace('Octopush\\Plumbok\\Annotation\\', '', get_class($annotation));
        $method = "apply$name";
        if (!method_exists($this, $method)) {
            throw new UnexpectedValueException("Unsupported annotation applied, given: $name");
        }
        $this->{$method}($annotation);
    }

    /**
     * @param mixed $annotation
     * @return bool
     */
    private function checkNonExcludingUsage(mixed $annotation): bool {
        $class = get_class($annotation);
        $appliedAnnotations = array_filter(array_map('get_class', $this->applied));
        foreach (self::$excludingAnnotations as $groupName => $excludingAnnotations) {
            if (
                in_array($class, $excludingAnnotations)
                && count(array_intersect($excludingAnnotations, $appliedAnnotations))
            ) {
                throw new UnexpectedValueException("Cannot use $class annotation because already applied excluding one");
            }
        }

        return true;
    }

    /**
     * @return boolean
     */
    public function requiresAllArgsConstructor(): bool {
        return $this->allArgsConstructor;
    }

    /**
     * @return boolean
     */
    public function requiresNoArgsConstructor(): bool {
        return $this->noArgsConstructor;
    }

    /**
     * @return boolean
     */
    public function requiresRequiredArgsConstructor(): bool {
        return $this->requiredArgsConstructor;
    }

    /**
     * @return boolean
     */
    public function requiresAllPropertyGetters(): bool {
        return $this->allPropertyGetters;
    }

    /**
     * @return boolean
     */
    public function requiresAllPropertySetters(): bool {
        return $this->allPropertySetters;
    }

    /**
     * @return boolean
     */
    public function requiresEqualTo(): bool {
        return $this->equalTo;
    }

	/**
	 * @param Property $property
	 * @return bool
	 */
    public function requiresToString(Property $property): bool {
        return !empty($this->toString) && $this->toString === $property->getName();
    }
}
