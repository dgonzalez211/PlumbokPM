<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 10.12.16
 * Time: 10:19
 */

namespace Octopush\Plumbok;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use Exception;
use Octopush\Plumbok\Annotation\Getter;
use Octopush\Plumbok\Annotation\Setter;
use Octopush\Plumbok\Compiler\Code\ClassReader;
use Octopush\Plumbok\Compiler\Code\PropertyReader;
use Octopush\Plumbok\Compiler\Context;
use Octopush\Plumbok\Compiler\GeneratorFactory;
use Octopush\Plumbok\Compiler\NodeFinder;
use Octopush\Plumbok\Compiler\Statements;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\ParserFactory;

/**
 * Class GeneratorParser
 * @package Plumbok
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Compiler
{
	/** @var Parser */
	private Parser $phpParser;
	/** @var DocParser */
	private DocParser $docParser;
	/** @var Serializer */
	private Serializer $docBlockSerializer;
	/** @var NodeFinder */
	private NodeFinder $nodeFinder;

	/**
	 * Compiler constructor.
	 */
	public function __construct() {
		$this->phpParser = (new ParserFactory)->create(ParserFactory::ONLY_PHP7);
		$this->docParser = new DocParser();
		$this->docParser->setIgnoreNotImportedAnnotations(true);
		$this->docParser->setIgnoredAnnotationNames(['package', 'author']);
		$this->docParser->addNamespace('Octopush\\Plumbok\\Annotation');
		foreach (['Value', 'Data', 'Getter', 'Setter', 'AllArgsConstructor', 'RequiredArgsConstructor', 'NoArgsConstructor', 'EqualTo', 'ToString'] as $file) {
			AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Annotation' . DIRECTORY_SEPARATOR . "$file.php");
		}
		$this->docBlockSerializer = new Serializer();
		$this->nodeFinder = new NodeFinder();
	}

	/**
	 * @param string $filename
	 * @return Node[]
	 * @throws Exception
	 */
	public function compile(string $filename): array {
		$nodes = $this->phpParser->parse(file_get_contents($filename));
		$namespaces = $this->nodeFinder->findNamespaces(...$nodes);
		$generated = false;
		foreach ($namespaces as $namespace) {
			$typeContext = new TypeContext((string)$namespace->name, $this->nodeFinder->findUses(...$namespace->stmts));
			foreach ($this->nodeFinder->findClasses(...$namespace->stmts) as $class) {
				$generated |= $this->processClass($class, $typeContext);
			}
		}
		if (empty($namespaces)) {
			$typeContext = new TypeContext('global', $this->nodeFinder->findUses(...$nodes));
			foreach ($this->nodeFinder->findClasses(...$nodes) as $class) {
				$generated |= $this->processClass($class, $typeContext);
			}
		}

		return $nodes;
	}

	/**
	 * @param Node\Stmt\Class_ $class
	 * @param TypeContext $typeContext
	 * @return bool
	 * @throws Exception
	 */
	private function processClass(Node\Stmt\Class_ $class, TypeContext $typeContext): bool {
		$statements = new Statements();
		$classReader = new ClassReader($this->docParser, $typeContext);
		$propertyReader = new PropertyReader($this->docParser, $typeContext);
		$generatorFactory = new GeneratorFactory($typeContext, $this->docBlockSerializer);
		$properties = $propertyReader->readProperties(
			$this->nodeFinder->findProperties(...$class->stmts),
			$this->nodeFinder->findMethods(...$class->stmts)
		);
		$classContext = new Context($classReader->readAnnotations($class));
		if ($classContext->requiresAllArgsConstructor()) {
			$statements->merge($generatorFactory->generateAllArgsConstructor($class->name->name, ...$properties));
		} elseif ($classContext->requiresRequiredArgsConstructor()) {
			$statements->merge($generatorFactory->generateRequiredArgsConstructor($class->name->name, ...$properties));
		} elseif ($classContext->requiresNoArgsConstructor()) {
			$statements->merge($generatorFactory->generateNoArgsConstructor($class->name->name));
		}
		if ($classContext->requiresEqualTo()) {
			$statements->merge($generatorFactory->generateEqualTo($class->name->name, ...$properties));
		}
		foreach ($properties as $property) {
			if ($classContext->requiresAllPropertyGetters()) {
				$statements->merge($generatorFactory->generateGetter($property->getName(), $property->getType()));
			}
			if ($classContext->requiresAllPropertySetters()) {
				$statements->merge($generatorFactory->generateSetter($property->getName(), $property->getType()));
			}
			foreach ($property->getAnnotations() as $annotation) {
				if ($annotation instanceof Getter && !$classContext->requiresAllPropertyGetters()) {
					$statements->merge($generatorFactory->generateGetter($property->getName(), $property->getType()));
				}
				if ($annotation instanceof Setter && !$classContext->requiresAllPropertySetters()) {
					$statements->merge($generatorFactory->generateSetter($property->getName(), $property->getType()));
				}
			}
			if ($classContext->requiresToString($property)) {
				$statements->merge($generatorFactory->generateToString($property->getName()));
			}
		}
		// remove @method tags from doc comment
		$classDocBlock = $classReader->readDocBlock($class);
		$tags = $classDocBlock->getTags();
		foreach ($tags as $index => $tag) {
			if ($tag instanceof DocBlock\Tags\Method) {
				unset($tags[$index]);
			}
		}
		$class->setDocComment($this->createDocComment($classDocBlock, ...$tags));
		// append new statements
		foreach ($statements as $statement) {
			$class->stmts[] = $statement;
		}

		return count($statements) > 0;
	}

	/**
	 * @param DocBlock $docBlock
	 * @param DocBlock\Tag ...$tags
	 * @return Doc
	 */
	private function createDocComment(DocBlock $docBlock, DocBlock\Tag ...$tags): Doc {
		$docComment = $this->docBlockSerializer->getDocComment(new DocBlock(
			$docBlock->getSummary(),
			$docBlock->getDescription(),
			$tags
		));

		$docComment = TagsUpdater::removeSpaceFromClassTags($docComment);

		return new Doc(str_replace("/**\n * \n *\n", "/**\n", $docComment));
	}
}
