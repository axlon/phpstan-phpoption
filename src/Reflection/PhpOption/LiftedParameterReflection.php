<?php

declare(strict_types=1);

namespace Resolve\PHPStan\Reflection\PhpOption;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\Type;

/**
 * @internal
 */
final class LiftedParameterReflection implements ParameterReflection
{
    public function __construct(
        private ParameterReflection $parameterReflection,
    ) {
    }

    public function getName(): string
    {
        return $this->parameterReflection->getName();
    }

    public function isOptional(): bool
    {
        return $this->parameterReflection->isOptional();
    }

    public function getType(): Type
    {
        return new GenericObjectType('PhpOption\Option', [$this->parameterReflection->getType()]);
    }

    public function passedByReference(): PassedByReference
    {
        return PassedByReference::createNo();
    }

    public function isVariadic(): bool
    {
        return $this->parameterReflection->isVariadic();
    }

    public function getDefaultValue(): ?Type
    {
        $rawDefaultValue = $this->parameterReflection->getDefaultValue();

        if ($rawDefaultValue === null) {
            return null;
        }

        return new GenericObjectType('PhpOption\Option', [$rawDefaultValue]);
    }
}
