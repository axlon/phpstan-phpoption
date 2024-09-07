<?php

declare(strict_types=1);

namespace Resolve\PHPStan\Type\PhpOption;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

/**
 * @see \PhpOption\Option::reject()
 * @internal
 */
final class RejectReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return 'PhpOption\Option';
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'reject';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope,
    ): ?Type {
        if ($methodCall->getArgs() === []) {
            return null;
        }

        $optionType = $scope->getType($methodCall->var);
        $valueType = $optionType->getTemplateType('PhpOption\Option', 'T');

        $rejectedType = TypeUtil::forShallowComparison(
            $scope->getType($methodCall->getArgs()[0]->value),
        );

        return new GenericObjectType('PhpOption\Option', [
            TypeCombinator::remove($valueType, $rejectedType),
        ]);
    }
}
