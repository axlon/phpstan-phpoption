<?php

declare(strict_types=1);

namespace Resolve\PHPStan\Type\PhpOption;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\NeverType;
use PHPStan\Type\Type;

/**
 * @see \PhpOption\Option::fromArraysValue()
 * @internal
 */
final class FromArraysValueReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return 'PhpOption\Option';
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'fromArraysValue';
    }

    public function getTypeFromStaticMethodCall(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope,
    ): ?Type {
        if (count($methodCall->getArgs()) <= 1) {
            return null;
        }

        $arrayType = $scope->getType($methodCall->getArgs()[0]->value);
        $keyType = $scope->getType($methodCall->getArgs()[1]->value);

        if (
            $keyType->isNull()->yes()
            || $arrayType->isOffsetAccessible()->no()
            || $arrayType->hasOffsetValueType($keyType)->no()
        ) {
            $valueType = new NeverType();
        } else {
            $valueType = $arrayType->getOffsetValueType($keyType);
        }

        return new GenericObjectType('PhpOption\Option', [
            $valueType->generalize(GeneralizePrecision::templateArgument()),
        ]);
    }
}
