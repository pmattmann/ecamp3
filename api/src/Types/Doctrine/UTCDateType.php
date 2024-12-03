<?php

namespace App\Types\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\Exception\InvalidFormat;

class UTCDateType extends DateType {
    private static ?\DateTimeZone $utc = null;

    /**
     * {@inheritdoc}
     *
     * @psalm-param T $value
     *
     * @return (T is null ? null : string)
     *
     * @template T
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string {
        if ($value instanceof \DateTime || $value instanceof \DateTimeImmutable) {
            $value = $value->setTimezone(self::getUtc());
        }

        if ($value instanceof \DateTimeImmutable) {
            return (new DateImmutableType())->convertToDatabaseValue($value, $platform);
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     *
     * @param T $value
     *
     * @return (T is null ? null : \DateTime)
     *
     * @throws ConversionException
     *
     * @template T
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\DateTime {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        $dateTime = \DateTime::createFromFormat('!'.$platform->getDateFormatString(), $value, self::getUtc());
        if (false !== $dateTime) {
            return $dateTime;
        }

        throw InvalidFormat::new(
            $value,
            static::class,
            $platform->getDateFormatString(),
        );
    }

    private static function getUtc(): \DateTimeZone {
        return self::$utc ?: self::$utc = new \DateTimeZone('UTC');
    }
}
