<?php

declare(strict_types=1);

namespace Resolve\PHPStan\Type\PhpOption;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\NullType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

/**
 * @see \PhpOption\Option::fromReturn()
 * @internal
 */
final class FromReturnReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return 'PhpOption\Option';
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'fromReturn';
    }

    public function getTypeFromStaticMethodCall(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope,
    ): ?Type {
        if ($methodCall->getArgs() === []) {
            return null;
        }

        $callbackType = $scope->getType($methodCall->getArgs()[0]->value);

        $noneValueType = isset($methodCall->getArgs()[2])
            ? TypeUtil::forShallowComparison($scope->getType($methodCall->getArgs()[2]->value))
            : new NullType();

        $callbackReturnType = ParametersAcceptorSelector::selectSingle(
            $callbackType->getCallableParametersAcceptors($scope),
        )->getReturnType();

        $valueType = TypeCombinator::remove($callbackReturnType, $noneValueType);

        return new GenericObjectType('PhpOption\LazyOption', [
            $valueType->generalize(GeneralizePrecision::templateArgument()),
        ]);
    }
}
