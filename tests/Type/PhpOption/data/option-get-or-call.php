<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 * @var \Closure(): int $callable
 */
assertType('int|string', $option->getOrCall($callable));
