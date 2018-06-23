<?php
namespace App\Measurement\Settings;

use App\Garment\GarmentType;
use App\Measurement\MeasurementType;

final class DefaultMeasurementSettings
{
    private const CENTIMETER_TO_MICROMETER =  10000;

    /** @var DefaultMeasurementSetting[] */
    private $settings;

    public function __construct()
    {
        $this->settings = [
            /*
             * Body
             */
            new DefaultMeasurementSetting(
                'measurements.default.body.height',
                MeasurementType::BODY(),
                [GarmentType::JACKET(), GarmentType::WAISTCOAT(), GarmentType::TROUSERS(), GarmentType::SHIRT()],
                 65 * self::CENTIMETER_TO_MICROMETER,
                120 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.body.neck',
                MeasurementType::BODY(),
                [GarmentType::JACKET(), GarmentType::WAISTCOAT(), GarmentType::SHIRT()],
                25 * self::CENTIMETER_TO_MICROMETER,
                65 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.body.chest',
                MeasurementType::BODY(),
                [GarmentType::JACKET(), GarmentType::WAISTCOAT(), GarmentType::SHIRT()],
                25 * self::CENTIMETER_TO_MICROMETER,
                65 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.body.bicep',
                MeasurementType::BODY(),
                [GarmentType::JACKET(), GarmentType::SHIRT()],
                25 * self::CENTIMETER_TO_MICROMETER,
                65 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.body.forearm',
                MeasurementType::BODY(),
                [GarmentType::JACKET(), GarmentType::SHIRT()],
                25 * self::CENTIMETER_TO_MICROMETER,
                65 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.body.natural_waist',
                MeasurementType::BODY(),
                [GarmentType::JACKET(), GarmentType::WAISTCOAT(), GarmentType::SHIRT()],
                25 * self::CENTIMETER_TO_MICROMETER,
                65 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.body.seat',
                MeasurementType::BODY(),
                [GarmentType::JACKET(), GarmentType::WAISTCOAT(), GarmentType::SHIRT()],
                25 * self::CENTIMETER_TO_MICROMETER,
                65 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.body.hips',
                MeasurementType::BODY(),
                [GarmentType::JACKET(), GarmentType::WAISTCOAT(), GarmentType::TROUSERS(), GarmentType::SHIRT()],
                25 * self::CENTIMETER_TO_MICROMETER,
                65 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.body.thigh',
                MeasurementType::BODY(),
                [GarmentType::TROUSERS()],
                25 * self::CENTIMETER_TO_MICROMETER,
                65 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.body.knee',
                MeasurementType::BODY(),
                [GarmentType::TROUSERS()],
                25 * self::CENTIMETER_TO_MICROMETER,
                65 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.body.calf',
                MeasurementType::BODY(),
                [GarmentType::TROUSERS()],
                25 * self::CENTIMETER_TO_MICROMETER,
                65 * self::CENTIMETER_TO_MICROMETER
            ),

            /*
             * Jacket
             */
            new DefaultMeasurementSetting(
                'measurements.default.jacket.shoulder_width',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.chest',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.back_width',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.half_back_width',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.waist',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.skirt',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.back_length_neck_to_waist',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.back_length_neck_to_hem',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.bicep',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.forearm',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.cuff',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.jacket.sleeve_length',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::JACKET()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),

            /*
             * Waistcoat
             */
            new DefaultMeasurementSetting(
                'measurements.default.waistcoat.chest',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.waistcoat.waist',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.waistcoat.hips',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.waistcoat.front_length',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.waistcoat.back_length',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),

            /*
             * Shirt
             */
            new DefaultMeasurementSetting(
                'measurements.default.shirt.neck',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.shirt.shoulder_width',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.shirt.chest',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.shirt.waist',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.shirt.hips',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.shirt.back_length',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.shirt.front_length',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.shirt.bicep',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.shirt.forearm',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.shirt.sleeve_length',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.shirt.cuff',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),

            /*
             * Trousers
             */
            new DefaultMeasurementSetting(
                'measurements.default.trousers.waistband',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.trousers.seat',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.trousers.thigh',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.trousers.knee',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.trousers.hem',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.trousers.crotch',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.trousers.outside_leg',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
            new DefaultMeasurementSetting(
                'measurements.default.trousers.inside_leg',
                MeasurementType::SAMPLE_ADJUSTMENT(),
                [GarmentType::WAISTCOAT()],
                -10 * self::CENTIMETER_TO_MICROMETER,
                 10 * self::CENTIMETER_TO_MICROMETER
            ),
        ];
    }

    /**
     * @return DefaultMeasurementSetting[]
     */
    public function getSettings(): array
    {
        return $this->settings;
    }
}