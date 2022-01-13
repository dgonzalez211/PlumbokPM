<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler;

use Exception;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Octopush\Plumbok\Compiler\Code\Property;
use Octopush\Plumbok\Compiler\Generator\AllArgsConstructor as AllArgsConstructorGenerator;
use Octopush\Plumbok\Compiler\Generator\EqualTo as EqualToGenerator;
use Octopush\Plumbok\Compiler\Generator\Getter as GetterGenerator;
use Octopush\Plumbok\Compiler\Generator\NoArgsConstructor as NoArgsConstructorGenerator;
use Octopush\Plumbok\Compiler\Generator\RequiredArgsConstructor as RequiredArgsConstructorGenerator;
use Octopush\Plumbok\Compiler\Generator\Setter as SetterGenerator;
use Octopush\Plumbok\Compiler\Generator\ToString as ToStringGenerator;

/**
 * Class GeneratorFactory
 * @package Octopush\Plumbok\Compiler
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class GeneratorFactory
{
    /** @var TypeContext */
    private TypeContext $typeContext;
    /** @var Serializer */
    private Serializer $docBlockSerializer;

    /**
     * GeneratorFactory constructor.
     * @param TypeContext $typeContext
     * @param Serializer $docBlockSerializer
     */
    public function __construct(TypeContext $typeContext, Serializer $docBlockSerializer) {
        $this->typeContext = $typeContext;
        $this->docBlockSerializer = $docBlockSerializer;
    }

	/**
	 * @param string $propertyName
	 * @param Type $type
	 * @return Statements
	 * @throws Exception
	 */
    public function generateGetter(string $propertyName, Type $type): Statements {
        $generator = new GetterGenerator($this->docBlockSerializer);
        $generator->setPropertyName($propertyName);
        $generator->setType($type);
        $generator->setTypeContext($this->typeContext);

        return $generator->generate();
    }

	/**
	 * @param string $propertyName
	 * @param Type $type
	 * @return Statements
	 * @throws Exception
	 */
    public function generateSetter(string $propertyName, Type $type): Statements {
        $generator = new SetterGenerator($this->docBlockSerializer);
        $generator->setPropertyName($propertyName);
        $generator->setType($type);
        $generator->setTypeContext($this->typeContext);

        return $generator->generate();
    }

	/**
	 * @param string $className
	 * @param Property[] $properties
	 * @return Statements
	 * @throws Exception
	 */
    public function generateAllArgsConstructor(string $className, Property ...$properties): Statements {
        $generator = new AllArgsConstructorGenerator($this->docBlockSerializer);
        $generator->setClassName($className);
        $generator->setTypeContext($this->typeContext);
        $generator->setProperties(...$properties);

        return $generator->generate();
    }

	/**
	 * @param string $className
	 * @param Property ...$properties
	 * @return Statements
	 * @throws Exception
	 */
    public function generateRequiredArgsConstructor(string $className, Property ...$properties): Statements {
        $generator = new RequiredArgsConstructorGenerator($this->docBlockSerializer);
        $generator->setClassName($className);
        $generator->setTypeContext($this->typeContext);
        $generator->setProperties(...$properties);

        return $generator->generate();
    }

    /**
     * @param string $className
     * @return Statements
     */
    public function generateNoArgsConstructor(string $className): Statements {
        $generator = new NoArgsConstructorGenerator($this->docBlockSerializer);
        $generator->setClassName($className);

        return $generator->generate();
    }

	/**
	 * @param string $className
	 * @param Property ...$properties
	 * @return Statements
	 */
    public function generateEqualTo(string $className, Property ...$properties): Statements {
        $generator = new EqualToGenerator($this->docBlockSerializer);
        $generator->setClassName($className);
        $generator->setTypeContext($this->typeContext);
        $generator->setProperties(...$properties);

        return $generator->generate();
    }

    public function generateToString(string $propertyName): Statements {
        $generator = new ToStringGenerator($this->docBlockSerializer);
        $generator->setPropertyName($propertyName);

        return $generator->generate();
    }
}
