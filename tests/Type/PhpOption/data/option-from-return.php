<?php

declare(strict_types=1);

use PhpOption\Option;

use function PHPStan\Testing\assertType;

/**
 * @var callable(): (string|null) $fn
 */
assertType('PhpOption\LazyOption<string>', Option::fromReturn($fn));
assertType('PhpOption\LazyOption<string|null>', Option::fromReturn($fn, noneValue: 123));

/**
 * @var callable(): void $fn
 */
assertType('PhpOption\LazyOption<*NEVER*>', Option::fromReturn($fn));
assertType('PhpOption\LazyOption<null>', Option::fromReturn($fn, noneValue: 123));
