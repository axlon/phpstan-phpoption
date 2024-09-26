<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string|null> $option
 */
assertType('PhpOption\Option<string>', $option->filter('is_string'));
assertType('PhpOption\Option<string>', $option->filter(static fn ($value) => is_string($value)));
assertType('PhpOption\Option<string>', $option->filter(is_string(...)));
