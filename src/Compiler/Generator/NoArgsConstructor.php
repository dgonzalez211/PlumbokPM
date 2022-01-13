<?php declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Generator;

use phpDocumentor\Reflection\DocBlock;
use PhpParser\Node;
use Octopush\Plumbok\Compiler;
use Octopush\Plumbok\Compiler\Statements;

/**
 * Class AllArgsConstructor
 * @package Octopush\Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class NoArgsConstructor extends GeneratorBase
{
    use WithClassName;

    /**
     * @return Compiler\Statements
     */
    public function generate(): Compiler\Statements
    {

        $docBlock = new DocBlock(
            $this->className . ' constructor.',
            null,
            []
        );
        $result = new Statements();
        $result->add(new Node\Stmt\ClassMethod(
            '__construct', [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
            ],[
                'comments' => [$this->createComment($docBlock)],
            ]
        ));

        return $result;

    }
}
