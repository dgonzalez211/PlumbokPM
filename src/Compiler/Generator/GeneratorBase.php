<?php
declare(strict_types=1);


namespace Octopush\Plumbok\Compiler\Generator;

use Octopush\Plumbok\Compiler;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Stmt\Expression;

/**
 * Class GeneratorBase
 * @package Octopush\Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
abstract class GeneratorBase
{
    /**
     * @return Compiler\Statements
     */
    abstract public function generate(): Compiler\Statements;

    /**
     * @var Serializer
     */
    private Serializer $docBlockSerializer;

    /**
     * GeneratorBase constructor.
     * @param Serializer $docBlockSerializer
     */
    public function __construct(Serializer $docBlockSerializer) {
        $this->docBlockSerializer = $docBlockSerializer;
    }

    /**
     * @param DocBlock $docblock
     * @return Comment
     */
    protected function createComment(DocBlock $docblock): Comment {
        return new Comment\Doc($this->docBlockSerializer->getDocComment($docblock));
    }

    /**
     * @param string $propertyName
     * @return Node\Stmt\Return_
     */
    protected function createReturnProperty(string $propertyName): Node\Stmt\Return_ {
        return new Node\Stmt\Return_(new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), $propertyName));
    }

    /**
     * @param string $propertyName
     * @param string|null $propertySetter
     * @return Expression
     */
    protected function createPropertyMutation(string $propertyName, string $propertySetter = null): Expression {
        // $this->{$propertyName} = $$propertyName;
        if (empty($propertySetter)) {
            return new Expression(new Node\Expr\Assign(
                    new Node\Expr\PropertyFetch(
                        new Node\Expr\Variable('this'),
                        $propertyName
                    ),
                    new Node\Expr\Variable($propertyName)
                )
            );
        }

        // $this->set{$propertyName}($$propertyName);
        return new Expression(new Node\Expr\MethodCall(
            new Node\Expr\Variable('this'),
            $propertySetter,
            [
                new Node\Arg(new Node\Scalar\String_($propertyName))
            ]
        ));
    }
}
