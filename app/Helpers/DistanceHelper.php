<?php

namespace App\Helpers;

use \App\Library\Distance\DistanceInterface;

class DistanceHelper
{
    /**
     * @var DistanceInterface
     */
    protected $distanceObj;

    /**
     * DistanceHelper constructor.
     *
     * @param DistanceInterface $distanceObj
     */
    public function __construct(DistanceInterface $distanceObj)
    {
        $this->distanceObj = $distanceObj;
    }

    /**
     * Fetches distance between two pairs of lat and long
     *
     * @param string $source
     * @param string $destination
     *
     * @return int Distance in meters
     */
    public function getDistance($source, $destination)
    {
        return $this->distanceObj->getDistance($source, $destination);
    }
}
