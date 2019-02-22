<?php

namespace App\Library\Distance;

class GoogleDistance implements DistanceInterface
{

    /**
     * @param string $source
     * @param string $destination
     *
     * @return int|string
     */
    public function getDistance($source, $destination)
    {
        $googleApiKey      = env('GOOGLE_API_KEY');
        $googleQueryString = env('GOOGLE_API_URL').'?units=imperial&origins='
            .$source.'&destinations='.$destination.'&key='.$googleApiKey;
        try {
            $responseData = file_get_contents($googleQueryString);
            $responseData = json_decode($responseData);

            if (empty($responseData) || $responseData->status != 'OK') {
                return (isset($responseData->status)) ? $responseData->status : 'GOOGLE_API_NULL_RESPONSE';
            }

            $dataElements = $responseData->rows[0]->elements[0];
            return (int) $dataElements->distance->value;
        } catch (\Exception $e) {
            return 'GOOGLE_API_NULL_RESPONSE';
        }
    }
}
