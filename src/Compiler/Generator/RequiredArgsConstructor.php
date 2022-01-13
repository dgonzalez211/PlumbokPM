<?php declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Generator;

use Exception;
use phpDocumentor\Reflection\DocBlock;
use PhpParser\Node;
use Octopush\Plumbok\Compiler;
use Octopush\Plumbok\Compiler\Code\Property;
use Octopush\Plumbok\Compiler\Statements;

/**
 * Class AllArgsConstructor
 * @package Octopush\Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class RequiredArgsConstructor extends GeneratorBase
{
    use WithClassName, WithTypeResolver, WithProperties;

    /**
     * @return Compiler\Statements
     * @throws Exception
     */
    public function generate(): Compiler\Statements
    {

        $docBlock = new DocBlock(
            $this->className . ' constructor.',
            null,
            array_map(function (Property $property) {
                return new DocBlock\Tags\Param($property->getName(), $property->getType());
            }, array_filter($this->properties, function (Property $property) {
                return !$property->hasDefaultValue();
            })),
            $this->typeContext
        );
        $result = new Statements();
        $result->add(new Node\Stmt\ClassMethod(
            '__construct', [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'params' => array_map(function (Property $property) {
                    return new Node\Param(
                        new Node\Expr\Variable($property->getName()),
                        null,
                        $this->resolveType($property->getType())
                    );
                }, array_filter($this->properties, function (Property $property) {
                    return !$property->hasDefaultValue();
                })),
                'stmts' => array_map(function (Property $property) {
                    return $this->createPropertyMutation($property->getName(), $property->getSetter());
                }, array_filter($this->properties, function (Property $property) {
                    return !$property->hasDefaultValue();
                })),
            ],[
                'comments' => [$this->createComment($docBlock)],
            ]
        ));

        return $result;

    }
}
