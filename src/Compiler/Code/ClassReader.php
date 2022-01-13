<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Code;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\DocParser;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Stmt\Class_;
use ReflectionException;

/**
 * Class ClassReader
 * @package Octopush\Plumbok\Compiler\Code
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class ClassReader
{
    /** @var DocParser */
    private DocParser $parser;
    /** @var Context */
    private Context $context;

    /**
     * ClassReader constructor.
     * @param DocParser $parser
     * @param Context $context
     */
    public function __construct(DocParser $parser, Context $context) {
        $this->parser = $parser;
        $this->context = $context;
    }

    /**
     * @param Class_ $class
     * @return array
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function readAnnotations(Class_ $class): array {
        return $this->parser->parse($class->getDocComment() !== null ? $class->getDocComment()->getText() : null);
    }

    /**
     * @param Class_ $class
     * @return DocBlock
     */
    public function readDocBlock(Class_ $class): DocBlock {
        if (empty((string)$class->getDocComment())) {
            return new DocBlock('', null, [], $this->context);
        }

        return DocBlockFactory::createInstance()->create($class->getDocComment() !== null ? (string)$class->getDocComment()->getReformattedText() : null, $this->context);
    }
}
