<?php

/**
 * copied 1:1 from https://github.com/doctrine/dbal/blob/4.2.x/tests/Types/BaseDateTypeTestCase.php.
 */

namespace App\Tests\Types\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class BaseDateTypeTestCase extends TestCase {
    protected AbstractPlatform&MockObject $platform;
    protected Type $type;

    /** @var non-empty-string */
    private string $currentTimezone;

    protected function setUp(): void {
        $this->platform = $this->getMockForAbstractClass(AbstractPlatform::class);
        $this->currentTimezone = \date_default_timezone_get();
    }

    protected function tearDown(): void {
        \date_default_timezone_set($this->currentTimezone);
    }

    public function testDateConvertsToDatabaseValue(): void {
        self::assertIsString($this->type->convertToDatabaseValue(new \DateTime(), $this->platform));
    }

    #[DataProvider('invalidPHPValuesProvider')]
    public function testInvalidTypeConversionToDatabaseValue(mixed $value): void {
        $this->expectException(ConversionException::class);

        $this->type->convertToDatabaseValue($value, $this->platform);
    }

    public function testNullConversion(): void {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertDateTimeToPHPValue(): void {
        $date = new \DateTime('now');

        self::assertSame($date, $this->type->convertToPHPValue($date, $this->platform));
    }

    /** @return mixed[][] */
    public static function invalidPHPValuesProvider(): iterable {
        return [
            [0],
            [''],
            ['foo'],
            ['10:11:12'],
            ['2015-01-31'],
            ['2015-01-31 10:11:12'],
            [new \stdClass()],
            [27],
            [-1],
            [1.2],
            [[]],
            [['an array']],
        ];
    }
}
