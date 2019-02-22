<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Distance extends Model
{
    /**
     * @var string
     */
    protected $table = 'distance';

    /**
     * Fetches distance between two geo coordinates.
     * fetch the distance already exist in the Distance table for the lat longs
     *
     *
     * @param string $sourceLatitude
     * @param string $sourceLongitude
     * @param string $destinationLatitude
     * @param string $destinationLongitude
     *
     * @return self
     */
    public function checkIfDistanceExist(
        $sourceLatitude,
        $sourceLongitude,
        $destinationLatitude,
        $destinationLongitude
    ) {

        //check if for the same source and destnation lat long already available then use that
        $distance = self::where([
            ['source_latitude', '=', $sourceLatitude],
            ['source_longitude', '=', $sourceLongitude],
            ['destination_latitude', '=', $destinationLatitude],
            ['destination_longitude', '=', $destinationLongitude],
        ])->first();

        return $distance;
    }

    public function saveDistance(
        $sourceLatitude,
        $sourceLongitude,
        $destinationLatitude,
        $destinationLongitude,
        $distanceBetween
    ) {

        //inserting data in distance table
        $distance                        = new Distance;
        $distance->source_latitude       = $sourceLatitude;
        $distance->source_longitude      = $sourceLongitude;
        $distance->destination_latitude  = $destinationLatitude;
        $distance->destination_longitude = $destinationLongitude;
        $distance->distance              = $distanceBetween;
        $distance->save();

        return $distance;
    }
}
