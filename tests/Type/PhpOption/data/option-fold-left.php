<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 */
assertType('int|string', $option->foldLeft(123, static function ($initial, $value) {
    assertType('int', $initial);
    assertType('string', $value);

    return $value;
}));
