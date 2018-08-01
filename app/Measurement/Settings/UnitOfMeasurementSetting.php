<?php
namespace App\Measurement\Settings;

use MyCLabs\Enum\Enum;

/**
 * @method static self INCHES()
 * @method static self CENTIMETERS()
 */
final class UnitOfMeasurementSetting extends Enum
{
    protected const INCHES      = 'INCHES';
    protected const CENTIMETERS = 'CENTIMETERS';

    private const ROUND_MEASUREMENT_TO_NEAREST = [
        self::INCHES      => 25400, // 1 inch in micrometers.
        self::CENTIMETERS => 10000, // 1 CM in micrometers.
    ];

    public static function DEFAULT(): self
    {
        return self::CENTIMETERS();
    }

    public function getRoundMeasurementToNearestValue(): int
    {
        return self::ROUND_MEASUREMENT_TO_NEAREST[$this->value];
    }
}