<?php
namespace App\Measurement\Settings;

use MyCLabs\Enum\Enum;

/**
 * @method static self INCHES()
 * @method static self CENTIMETERS()
 */
final class UnitOfMeasurementSetting extends Enum
{
    protected const INCHES = 'INCHES';
    protected const CENTIMETERS = 'CENTIMETERS';

    public static function DEFAULT(): self
    {
        return self::CENTIMETERS();
    }
}