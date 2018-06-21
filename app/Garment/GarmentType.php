<?php
namespace App\Garment;

use MyCLabs\Enum\Enum;

/**
 * @method static self JACKET()
 * @method static self WAISTCOAT()
 * @method static self SHIRT()
 * @method static self TROUSERS()
 */
final class GarmentType extends Enum
{
    protected const JACKET    = 'JACKET';
    protected const WAISTCOAT = 'WAISTCOAT';
    protected const SHIRT     = 'SHIRT';
    protected const TROUSERS  = 'TROUSERS';
}