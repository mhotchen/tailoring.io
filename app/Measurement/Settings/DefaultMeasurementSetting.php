<?php
namespace App\Measurement\Settings;

use App\Garment\GarmentType;
use App\Measurement\MeasurementType;

final class DefaultMeasurementSetting
{
    /** @var string */
    private $name;

    /** @var MeasurementType */
    private $type;

    /** @var GarmentType[] */
    private $garmentTypes;

    /** @var int */
    private $minValue;

    /** @var int */
    private $maxValue;

    /**
     * @param string          $name
     * @param MeasurementType $type
     * @param GarmentType[]   $garmentTypes
     * @param int             $minValue
     * @param int             $maxValue
     */
    public function __construct(
        string $name,
        MeasurementType $type,
        array $garmentTypes,
        int $minValue,
        int $maxValue
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->garmentTypes = $garmentTypes;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return MeasurementType
     */
    public function getType(): MeasurementType
    {
        return $this->type;
    }

    /**
     * @return GarmentType[]
     */
    public function getGarmentTypes(): array
    {
        return $this->garmentTypes;
    }

    /**
     * @return int
     */
    public function getMinValue(): int
    {
        return $this->minValue;
    }

    /**
     * @return int
     */
    public function getMaxValue(): int
    {
        return $this->maxValue;
    }
}