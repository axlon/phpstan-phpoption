<?php

declare(strict_types=1);

namespace Tests\Type\PhpOption;

use Generator;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TypeInferenceTest extends TypeInferenceTestCase
{
    public static function getAdditionalConfigFiles(): array
    {
        return [
            ...parent::getAdditionalConfigFiles(),
            __DIR__ . '/../../../extension.neon',
        ];
    }

    public static function provideFileAsserts(): Generator
    {
        yield from self::gatherAssertTypesFromDirectory(__DIR__ . '/data');
    }

    #[DataProvider('provideFileAsserts')]
    public function testFileAsserts(string $assertType, string $file, mixed ...$args): void
    {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }
}
