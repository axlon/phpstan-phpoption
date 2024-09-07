<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<int> $option
 */
assertType('PhpOption\Option<float>', $option->map(static function ($value) {
    assertType('int', $value);

    return $value * 1.;
}));
