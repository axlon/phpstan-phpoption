<?php

declare(strict_types=1);

namespace Resolve\PHPStan\Type\PhpOption;

use PHPStan\Type\NeverType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeTraverser;
use PHPStan\Type\UnionType;

/**
 * @internal
 */
final class TypeUtil
{
    public static function forShallowComparison(Type $type): Type
    {
        return TypeTraverser::map($type, static function (Type $type, callable $traverse): Type {
            if ($type instanceof UnionType) {
                return $traverse($type);
            }

            if ($type->isConstantScalarValue()->yes() || $type->isConstantArray()->yes()) {
                return $type;
            }

            return new NeverType();
        });
    }
}
