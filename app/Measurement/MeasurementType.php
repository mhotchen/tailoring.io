<?php
namespace App\Measurement;

use MyCLabs\Enum\Enum;

/**
 * @method static self BODY()
 * @method static self GARMENT()
 * @method static self SAMPLE_ADJUSTMENT()
 * @method static self ALTERATION()
 */
final class MeasurementType extends Enum
{
    protected const BODY              = 'BODY';
    protected const GARMENT           = 'GARMENT';
    protected const SAMPLE_ADJUSTMENT = 'SAMPLE_ADJUSTMENT';
    protected const ALTERATION        = 'ALTERATION';
}