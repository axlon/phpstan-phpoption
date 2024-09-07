<?php

declare(strict_types=1);

namespace Resolve\PHPStan\Type\PhpOption;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\NullType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

/**
 * @see \PhpOption\Option::fromValue()
 * @internal
 */
final class FromValueReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return 'PhpOption\Option';
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'fromValue';
    }

    public function getTypeFromStaticMethodCall(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope,
    ): ?Type {
        if ($methodCall->getArgs() === []) {
            return null;
        }

        $noneValueType = isset($methodCall->getArgs()[1])
            ? TypeUtil::forShallowComparison($scope->getType($methodCall->getArgs()[1]->value))
            : new NullType();

        $valueType = TypeCombinator::remove(
            $scope->getType($methodCall->getArgs()[0]->value),
            $noneValueType,
        );

        return new GenericObjectType('PhpOption\Option', [
            $valueType->generalize(GeneralizePrecision::templateArgument()),
        ]);
    }
}
