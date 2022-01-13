<?php declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Generator;

use Exception;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Octopush\Plumbok\Compiler\Statements;

/**
 * Class Getter
 * @package Octopush\Plumbok\Compiler
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Getter extends GeneratorBase
{
    use WithPropertyName, WithType, WithTypeResolver;

    /**
     * @return Statements
     * @throws Exception
     */
    public function generate(): Statements {
        $docBlock = new DocBlock(
            'Retrieves ' . $this->propertyName,
            null,
            [new DocBlock\Tags\Return_($this->type)],
            $this->typeContext
        );
        $functionName = 'get' . ucfirst($this->propertyName);
        if (is_a($this->type, Boolean::class)) {
            $functionName = 'is' . ucfirst($this->propertyName);
        }
        $result = new Statements();
        $result->add(new Node\Stmt\ClassMethod(
            $functionName, [
            'flags' => Class_::MODIFIER_PUBLIC,
            'stmts' => [$this->createReturnProperty($this->propertyName)],
            'returnType' => $this->resolveType($this->type) ?: null,
        ], [
                'comments' => [$this->createComment($docBlock)],
            ]
        ));

        return $result;
    }
}
