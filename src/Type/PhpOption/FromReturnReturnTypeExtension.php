<?php

declare(strict_types=1);

namespace Resolve\PHPStan\Type\PhpOption;

use PhpParser\Node\Arg;
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

        $parametersAcceptors = $callbackType->getCallableParametersAcceptors($scope);

        $args = isset($methodCall->getArgs()[1])
            ? [new Arg($methodCall->getArgs()[1]->value, unpack: true)]
            : [];

        $returnType = ParametersAcceptorSelector::selectFromArgs($scope, $args, $parametersAcceptors)->getReturnType();
        $valueType = TypeCombinator::remove(TypeUtil::replaceVoid($returnType), $noneValueType);

        return new GenericObjectType('PhpOption\LazyOption', [
            $valueType->generalize(GeneralizePrecision::templateArgument()),
        ]);
    }
}
