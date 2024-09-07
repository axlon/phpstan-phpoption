<?php

declare(strict_types=1);

use PhpOption\Option;

use function PHPStan\Testing\assertType;

/**
 * @var callable(): (string|null) $fn
 */
assertType('PhpOption\LazyOption<string>', Option::fromReturn($fn));
assertType('PhpOption\LazyOption<string>', Option::fromReturn($fn, [], null));
assertType('PhpOption\LazyOption<string|null>', Option::fromReturn($fn, [], 123));
