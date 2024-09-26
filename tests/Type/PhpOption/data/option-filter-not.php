<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @var \PhpOption\Option<string|null> $option
 */
assertType('PhpOption\Option<string>', $option->filterNot('is_null'));
assertType('PhpOption\Option<string>', $option->filterNot(static fn ($value) => is_null($value)));
assertType('PhpOption\Option<string>', $option->filterNot(is_null(...)));
