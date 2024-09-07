<?php

declare(strict_types=1);

use PhpOption\Some;

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string> $option
 */
assertType('PhpOption\Option<int|string>', $option->orElse(new Some(123)));
