<?php

declare(strict_types=1);

use PhpOption\Option;

use function PHPStan\Testing\assertType;

/**
 * @var array<array-key, mixed> $array
 */
assertType('PhpOption\Option<mixed>', Option::fromArraysValue($array, 'key'));

/**
 * @var array{foo: 123, bar?: 1.2, baz: null} $array
 */
assertType('PhpOption\Option<int>', Option::fromArraysValue($array, 'foo'));
assertType('PhpOption\Option<float>', Option::fromArraysValue($array, 'bar'));
assertType('PhpOption\Option<null>', Option::fromArraysValue($array, 'baz'));
assertType('PhpOption\Option<*NEVER*>', Option::fromArraysValue($array, 'qux'));
