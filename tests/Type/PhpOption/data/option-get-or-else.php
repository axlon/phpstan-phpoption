<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 * @var int $default
 */
assertType('int|string', $option->getOrElse($default));
