<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 */
assertType('int|string', $option->getOrCall(static fn () => 123));