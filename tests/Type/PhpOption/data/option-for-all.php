<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 */
assertType('PhpOption\Option<string>', $option->forAll(static function ($value) {
    assertType('string', $value);
}));
