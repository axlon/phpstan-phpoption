<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 */
assertType('PhpOption\Option<string>', $option->select('string'));
assertType('PhpOption\Option<never>', $option->select(null));
assertType('PhpOption\Option<never>', $option->select(123));

/**
 * @var \PhpOption\Option<string|null> $option
 */
assertType('PhpOption\Option<string>', $option->select('string'));
assertType('PhpOption\Option<never>', $option->select(123));
assertType('PhpOption\Option<null>', $option->select(null));
