<?php

namespace App\Validators;

class RouteCoordinatesValidator
{
    /**
     * @var string
     */
    protected $error;

    public function getError()
    {
        return $this->error;
    }

    /**
     * function validates validity of given input GEO lat longs
     *
     * @param float $sourceLatitude
     * @param float $sourceLongitude
     * @param float $destinationLatitude
     * @param float $destinationLongitude
     *
     * @return bool
     */
    public function validate(
        $sourceLatitude,
        $sourceLongitude,
        $destinationLatitude,
        $destinationLongitude
    ) {
    
        if ($sourceLatitude == $destinationLatitude && $sourceLongitude == $destinationLongitude) {
            $this->error = 'REQUESTED_SOURCE_DESTINATION_SAME';
        } elseif (!$sourceLatitude || !$sourceLongitude || !$destinationLatitude
            || !$destinationLongitude) {
            $this->error = 'REQUEST_PARAMETER_MISSING';
        } elseif ($sourceLatitude < -90 || $sourceLatitude > 90 || $destinationLatitude
            < -90 || $destinationLatitude > 90) {
            $this->error = 'LATITUDE_OUT_OF_RANGE';
        } elseif ($sourceLongitude < -180 || $sourceLongitude > 180 || $destinationLongitude
            < -180 || $destinationLongitude > 180) {
            $this->error = 'LONGITUDE_OUT_OF_RANGE';
        } elseif (!is_numeric($sourceLatitude) || !is_numeric($destinationLatitude)
            || !is_numeric($sourceLongitude) || !is_numeric($destinationLongitude)) {
            $this->error = 'INVALID_PARAMETERS';
        }

        return $this->error ? false : true;
    }
}
