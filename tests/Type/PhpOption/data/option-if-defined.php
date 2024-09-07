<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 */
assertType('null', $option->ifDefined(static function ($value) {
    assertType('string', $value);
}));
