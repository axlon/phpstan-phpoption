<?php

declare(strict_types=1);

use PhpOption\Option;

use function PHPStan\Testing\assertType;

/**
 * @var string|null $value
 */
assertType('PhpOption\Option<string>', Option::ensure($value));
assertType('PhpOption\Option<null>|PhpOption\Option<string>', Option::ensure($value, 123));

/**
 * @var \PhpOption\Option<string> $option
 */
assertType('PhpOption\Option<string>', Option::ensure($option));

/**
 * @var \PhpOption\Option<string>|123|(callable(): float)|null $option
 */
assertType('PhpOption\Option<float>|PhpOption\Option<int>|PhpOption\Option<string>', Option::ensure($option));
assertType('PhpOption\Option<float>|PhpOption\Option<null>|PhpOption\Option<string>', Option::ensure($option, 123));

/**
 * @var callable(): string $value
 */
assertType('PhpOption\Option<string>', Option::ensure($value));

/**
 * @var callable(): void $value
 */
assertType('PhpOption\Option<*NEVER*>', Option::ensure($value));
assertType('PhpOption\Option<null>', Option::ensure($value, 123));
