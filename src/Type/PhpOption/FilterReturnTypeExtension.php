<?php

declare(strict_types=1);

namespace Resolve\PHPStan\Type\PhpOption;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\Type;

/**
 * @see \PhpOption\Option::filter()
 * @see \PhpOption\Option::filterNot()
 * @internal
 */
final class FilterReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return 'PhpOption\Option';
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return in_array($methodReflection->getName(), ['filter', 'filterNot'], true);
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope,
    ): ?Type {
        if ($methodCall->getArgs() === []) {
            return null;
        }

        $callbackArg = $methodCall->getArgs()[0]->value;

        if ($callbackArg instanceof ArrowFunction && $callbackArg->params !== []) {
            $expr = $callbackArg->expr;
            $var = $callbackArg->params[0]->var;
        } elseif (
            $callbackArg instanceof Closure
            && $callbackArg->params !== []
            && $callbackArg->stmts !== []
            && $callbackArg->stmts[0] instanceof Return_
            && $callbackArg->stmts[0]->expr !== null
        ) {
            $expr = $callbackArg->stmts[0]->expr;
            $var = $callbackArg->params[0]->var;
        } elseif ($callbackArg instanceof String_) {
            $var = new Variable('value');
            $expr = new FuncCall(
                match ($callbackArg->value[0]) {
                    '\\' => new FullyQualified(ltrim($callbackArg->value, '\\')),
                    default => new Name($callbackArg->value),
                },
                [new Arg($var)],
            );
        } elseif (
            (
                $callbackArg instanceof FuncCall
                || $callbackArg instanceof MethodCall
                || $callbackArg instanceof StaticCall
            )
            && $callbackArg->isFirstClassCallable()
        ) {
            $var = new Variable('value');
            $expr = clone $callbackArg;
            $expr->args = [new Arg($var)];
        } else {
            return null;
        }

        if ($var instanceof Variable === false) {
            return null;
        }

        $valueType = $scope->getType(new MethodCall($methodCall->var, 'get'));
        $scope = $scope->assignExpression($var, $valueType, $valueType);

        if ($methodReflection->getName() === 'filter') {
            $scope = $scope->filterByTruthyValue($expr);
        } else {
            $scope = $scope->filterByFalseyValue($expr);
        }

        return new GenericObjectType('PhpOption\Option', [
            $scope->getVariableType($var->name),
        ]);
    }
}
