<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Code;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\DocParser;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Mixed_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property as ClassProperty;
use ReflectionException;

/**
 * Class PropertyReader
 * @package Octopush\Plumbok\Compiler\Code
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class PropertyReader
{
    /** @var DocParser */
    private DocParser $parser;
    /** @var Context */
    private Context $context;

    /**
     * PropertyReader constructor.
     * @param DocParser $parser
     * @param Context $context
     */
    public function __construct(DocParser $parser, Context $context) {
        $this->parser = $parser;
        $this->context = $context;
    }

    /**
     * @param ClassProperty $property
     * @return array
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function readAnnotations(ClassProperty $property): array {
        return $property->getDocComment() ? $this->parser->parse($property->getDocComment()->getText()) : [];
    }

    /**
     * @param ClassProperty $property
     * @return DocBlock
     */
    public function readDocBlock(ClassProperty $property): DocBlock {
        return DocBlockFactory::createInstance()->create((string)$property->getDocComment(), $this->context);
    }

    /**
     * @param ClassProperty[] $classProperties
     * @param ClassMethod[] $classMethods
     * @return array
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function readProperties(array $classProperties, array $classMethods): array {
        $properties = [];
        foreach ($classProperties as $property) {
            $propertyDocBlock = $this->readDocBlock($property);
            /** @var DocBlock\Tags\Var_[] $varTags */
            if (count($varTags = $propertyDocBlock->getTagsByName('var'))) {
                $type = $varTags[0]->getType();
            }
            foreach ($property->props as $prop) {
                $setter = '';
                foreach ($classMethods as $method) {
                    if ($method->name->name == 'set' . ucfirst($prop->name->name)) {
                        $setter = $method->name->name;
                    }
                }
                $properties[] = new Property($prop->name->name, $type ?? new Mixed_(), $prop->default !== null, $setter, $this->readAnnotations($property));
            }
        }

        return $properties;
    }
}
