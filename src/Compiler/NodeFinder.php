<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Compiler;

use PhpParser\Node;

/**
 * Class NodeFinder
 * @package Octopush\Plumbok\Compiler
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class NodeFinder
{
	/**
	 * @param Node ...$nodes
	 * @return Node\Stmt\Namespace_[]
	 */
    public function findNamespaces(Node ...$nodes) : array
    {
        $namespaces = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\Namespace_) {
                $namespaces[] = $node;
            }
        }

        return $namespaces;
    }

	/**
	 * @param Node ...$nodes
	 * @return string[]
	 */
    public function findUses(Node ...$nodes) : array
    {
        $uses = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\GroupUse) {
                foreach ($node->uses as $use) {
                    $uses[$use->alias] = $node->prefix->toString() . '\\' . $use->name->toString();
                }
            }
            if ($node instanceof Node\Stmt\Use_) {
                foreach ($node->uses as $use) {
                    if(!empty($use->alias)) {
                        $uses[$use->alias->name] = $use->name->toString();
                    } else {
                        $uses[] = $use->name->toString();
                    }
                }
            }
        }

        return $uses;
    }

	/**
	 * @param Node ...$nodes
	 * @return Node\Stmt\Property[]
	 */
    public function findProperties(Node ...$nodes) : array
    {
        $properties = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\Property) {
                $properties[] = $node;
            }
        }

        return $properties;
    }

	/**
	 * @param Node ...$nodes
	 * @return Node\Stmt\Class_[]
	 */
    public function findClasses(Node ...$nodes) : array
    {
        $classes = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\Class_) {
                $classes[] = $node;
            }
            if (property_exists($node, 'stmts') && count($node->stmts)) {
                $classes += $this->findClasses(...$node->stmts);
            }
        }

        return $classes;
    }

	/**
	 * @param Node ...$nodes
	 * @return Node\Stmt\ClassMethod[]
	 */
    public function findMethods(Node ...$nodes) : array
    {
        $methods = [];
        foreach ($nodes as $node) {
            if ($node instanceof Node\Stmt\ClassMethod) {
                $methods[] = $node;
            }
        }

        return $methods;
    }
}
