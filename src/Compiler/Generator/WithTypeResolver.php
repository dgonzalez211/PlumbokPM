<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler\Generator;

use Exception;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Null_;

/**
 * Class WithTypeResolver
 * @package Octopush\Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
trait WithTypeResolver
{
    /** @var Context */
    private Context $typeContext;

    /**
     * @param Context $typeContext
     */
    public function setTypeContext(Context $typeContext): void {
        $this->typeContext = $typeContext;
    }

    /**
     * @param Type $type
     * @return string
     * @throws Exception
     */
    private function resolveType(Type $type): string {
        $nullable = false;
        if ($type instanceof Compound) {
            if ($type->getIterator()->count() > 2) {
                throw new Exception("Too many types!");
            }

            $nullable = $this->isTypeNullable($type);
            $type = $this->getMainType($type);
        }

        if ($type instanceof Array_) {
            return 'array';
        }
        foreach ($this->typeContext->getNamespaceAliases() as $alias => $namespace) {
            if ((string)$type === "\\$namespace") {
                return $alias;
            }
        }

        return ($nullable && $type !== null ? '?' : '') . $type;
    }

    private function getMainType(Compound $type): ?Type {
        foreach ($type as $typeObj) {
            if (!($typeObj instanceof Null_)) {
                return $typeObj;
            }
        }
        return null;
    }

    /**
     * @param Compound $type
     * @return bool
     */
    private function isTypeNullable(Compound $type): bool {
        foreach ($type as $typeObj) {
            if ($typeObj instanceof Null_) {
                return true;
            }
        }

        return false;
    }
}
