<?php
namespace App\Measurement\Profile;

use MyCLabs\Enum\Enum;

/**
 * @method static self BODY()
 * @method static self GARMENT()
 */
final class MeasurementProfileType extends Enum
{
    protected const BODY    = 'BODY';
    protected const GARMENT = 'GARMENT';
}