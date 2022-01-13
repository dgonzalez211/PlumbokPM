<?php
declare(strict_types = 1);

namespace Octopush\Plumbok\Compiler\Generator;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Types\String_;
use PhpParser\Node;
use Octopush\Plumbok\Compiler;
use Octopush\Plumbok\Compiler\Statements;

/**
 * Class ToString
 * @package Octopush\Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class ToString extends GeneratorBase
{
    use WithPropertyName;

    /**
     * @return Compiler\Statements
     */
    public function generate(): Compiler\Statements
    {
        $docBlock = new DocBlock(
            'Returns string from $' . $this->propertyName,
            null,
            [new Return_(new String_())]
        );
        $result = new Statements();
        $result->add(new Node\Stmt\ClassMethod(
            'toString',
            [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'stmts' => [
                    new Node\Stmt\Return_(new Node\Expr\Cast\String_(new Node\Expr\PropertyFetch(
                        new Node\Expr\Variable('this'),
                        $this->propertyName
                    ))),
                ],
                'returnType' => 'string',
            ],[
                'comments' => [$this->createComment($docBlock)],
            ]
        ));

        return $result;
    }
}