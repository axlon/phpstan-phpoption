<?php

declare(strict_types=1);

namespace Resolve\PHPStan\Type\PhpOption;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Callables\CallableParametersAcceptor;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Type\ClosureType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\NeverType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Resolve\PHPStan\Reflection\PhpOption\LiftedParameterReflection;

/**
 * @see \PhpOption\Option::lift()
 * @internal
 */
final class LiftReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return 'PhpOption\Option';
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'lift';
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

        if (isset($methodCall->getArgs()[1])) {
            $noneValueType = TypeUtil::forShallowComparison($scope->getType($methodCall->getArgs()[1]->value));
        } else {
            $noneValueType = new NullType();
        }

        return TypeCombinator::union(...array_map(
            static function (CallableParametersAcceptor $parametersAcceptor) use ($noneValueType) {
                $parameters = array_map(
                    static function (ParameterReflection $parameterReflection) {
                        return new LiftedParameterReflection($parameterReflection);
                    },
                    $parametersAcceptor->getParameters(),
                );

                $returnTypeIsOption = (new ObjectType('PhpOption\Option'))->isSuperTypeOf(
                    $parametersAcceptor->getReturnType(),
                );

                if ($returnTypeIsOption->yes() || $returnTypeIsOption->maybe()) {
                    $returnType = $parametersAcceptor->getReturnType();
                } else {
                    $returnType = new NeverType();
                }

                if ($returnTypeIsOption->no() || $returnTypeIsOption->maybe()) {
                    $returnType = TypeCombinator::union(
                        $returnType,
                        new GenericObjectType('PhpOption\Option', [
                            TypeCombinator::remove($parametersAcceptor->getReturnType(), $noneValueType),
                        ]),
                    );
                }

                return new ClosureType($parameters, $returnType);
            },
            $callbackType->getCallableParametersAcceptors($scope),
        ));
    }
}
