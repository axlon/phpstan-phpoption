<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 * @var int $initialValue
 */
assertType('int|string', $option->foldLeft($initialValue, static function ($initialValue, $value) {
    assertType('int', $initialValue);
    assertType('string', $value);

    return $value;
}));
