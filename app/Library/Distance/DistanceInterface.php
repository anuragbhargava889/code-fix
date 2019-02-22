<?php

namespace App\Library\Distance;

interface DistanceInterface
{

    /**
     * Returns distance between Origin and Destination in meters
     * In case of any error send error code in string format
     *
     * @param string $origin
     * @param string $destination
     *
     * @return int|string
     */
    public function getDistance($origin, $destination);
}
