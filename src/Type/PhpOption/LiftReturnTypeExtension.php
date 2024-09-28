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
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeTraverser;
use PHPStan\Type\UnionType;
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

                $returnType = TypeTraverser::map(
                    $parametersAcceptor->getReturnType(),
                    static function (Type $type, callable $traverse) use ($noneValueType): Type {
                        if ($type instanceof UnionType) {
                            return $traverse($type);
                        }

                        if ((new ObjectType('PhpOption\Option'))->isSuperTypeOf($type)->yes()) {
                            return $type;
                        }

                        $type = TypeCombinator::remove(TypeUtil::replaceVoid($type), $noneValueType);

                        return new GenericObjectType('PhpOption\Option', [
                            $type->generalize(GeneralizePrecision::templateArgument()),
                        ]);
                    },
                );

                return new ClosureType($parameters, $returnType);
            },
            $callbackType->getCallableParametersAcceptors($scope),
        ));
    }
}
