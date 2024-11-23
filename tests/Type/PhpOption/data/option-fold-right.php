<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 * @var int $initialValue
 */
assertType('int|string', $option->foldRight($initialValue, static function ($value, $initialValue) {
    assertType('int', $initialValue);
    assertType('string', $value);

    return $value;
}));
