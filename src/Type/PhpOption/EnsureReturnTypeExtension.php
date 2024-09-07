<?php

declare(strict_types=1);

namespace Resolve\PHPStan\Type\PhpOption;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Callables\CallableParametersAcceptor;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeTraverser;
use PHPStan\Type\UnionType;

/**
 * @see \PhpOption\Option::ensure()
 * @internal
 */
final class EnsureReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return 'PhpOption\Option';
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'ensure';
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

        return TypeTraverser::map(
            $scope->getType($methodCall->getArgs()[0]->value),
            static function (Type $type, callable $traverse) use ($noneValueType, $scope): Type {
                if ($type instanceof UnionType) {
                    return $traverse($type);
                }

                if ((new ObjectType('PhpOption\Option'))->isSuperTypeOf($type)->yes()) {
                    return $type;
                }

                if ($type->isCallable()->yes()) {
                    $type = TypeCombinator::union(...array_map(
                        static fn (CallableParametersAcceptor $variant) => $variant->getReturnType(),
                        $type->getCallableParametersAcceptors($scope),
                    ));
                }

                $valueType = TypeCombinator::remove($type, $noneValueType);

                return new GenericObjectType('PhpOption\Option', [
                    $valueType->generalize(GeneralizePrecision::templateArgument()),
                ]);
            },
        );
    }
}
