<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 */
assertType('int|string', $option->foldRight(123, static function ($value, $initial) {
    assertType('int', $initial);
    assertType('string', $value);

    return $value;
}));
