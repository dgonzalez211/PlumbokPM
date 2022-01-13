<?php
declare(strict_types=1);

namespace Octopush\Plumbok\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("property", type = "string", required = true)
 * })
 * @property-read string $property
 */
final class ToString
{
    private string $property;

    public function __construct(array $values) {
        $this->property = $values['property'];
    }

    public function __get(string $name): string {
        return $name === 'property' ? $this->property : '';
    }
}
