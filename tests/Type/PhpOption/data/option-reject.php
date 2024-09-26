<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string|null> $option
 */
assertType('PhpOption\Option<non-empty-string|null>', $option->reject(''));
assertType('PhpOption\Option<string|null>', $option->reject('string'));
assertType('PhpOption\Option<string|null>', $option->reject(123));
assertType('PhpOption\Option<string>', $option->reject(null));

/**
 * @var \PhpOption\Option<array<string>> $option
 */
assertType('PhpOption\Option<non-empty-array<string>>', $option->reject([]));
assertType('PhpOption\Option<array<string>>', $option->reject(''));
