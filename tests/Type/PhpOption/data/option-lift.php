<?php

declare(strict_types=1);

use PhpOption\Option;

use function PHPStan\Testing\assertType;

/**
 * @var callable(int): string $fn
 */
assertType('Closure(PhpOption\Option<int>): PhpOption\Option<string>', Option::lift($fn));

/**
 * @var callable(int=): string $fn
 */
assertType('Closure(PhpOption\Option<int>=): PhpOption\Option<string>', Option::lift($fn));

/**
 * @var callable(int ...): string $fn
 */
assertType('Closure(PhpOption\Option<int> ...): PhpOption\Option<string>', Option::lift($fn));


/**
 * @var callable(): void $fn
 */
assertType('Closure(): PhpOption\Option<never>', Option::lift($fn));
assertType('Closure(): PhpOption\Option<null>', Option::lift($fn, 123));
