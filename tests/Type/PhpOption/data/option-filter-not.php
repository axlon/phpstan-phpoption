<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string|null> $option
 */
assertType('PhpOption\Option<null>', $option->filterNot('is_string'));
assertType('PhpOption\Option<null>', $option->filterNot(static fn ($value) => is_string($value)));
