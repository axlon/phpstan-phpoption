<?php

declare(strict_types=1);

use PhpOption\Some;

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string|null> $option
 */
assertType('PhpOption\Some<string>', $option->flatMap(static function ($value) {
    return new Some($value ?? 'string');
}));
