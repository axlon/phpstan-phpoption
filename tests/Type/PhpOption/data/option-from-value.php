<?php

declare(strict_types=1);

use PhpOption\Option;

use function PHPStan\Testing\assertType;

/**
 * @var string|null $value
 */
assertType('PhpOption\Option<string>', Option::fromValue($value));
assertType('PhpOption\Option<string>', Option::fromValue($value, null));
assertType('PhpOption\Option<string|null>', Option::fromValue($value, 123));
