<?php

namespace App\Helpers;

class ResponseMessageHelper
{
    protected static $messages = [
        'invalid_data'                      => 'INVALID DATA',
        'status_is_invalid'                 => 'STATUS_IS_INVALID',
        'invalid_id'                        => 'INVALID_ID',
        'order_taken'                       => 'ORDER_ALREADY_BEEN_TAKEN',
        'success'                           => 'SUCCESS',
        'invalid_google_key'                => 'THE_PROVIDED_API_KEY_IS_INVALID.',
        'INVALID_PARAMETER_TYPE'            => 'INVALID_PARAMETER_TYPE',
        'REQUEST_PARAMETER_MISSING'         => 'REQUEST_PARAMETER_MISSING',
        'LATITUDE_OUT_OF_RANGE'             => 'LATITUDE_RANGE_IS_NOT_VALID',
        'LONGITUDE_OUT_OF_RANGE'            => 'LONGITUDE_RANGE_IS_NOT_VALID',
        'NO_DATA_FOUND'                     => 'NO_DATA_FOUND',
        'REQUESTED_ORIGIN_DESTINATION_SAME' => 'REQUESTED_ORIGIN_AND_DESTINATION_IS_SAME',
        'REQUEST_DENIED'                    => 'REQUEST_TO_GOOGLE_DISTANCE_API_IS_DENIED',
        'OVER_QUERY_LIMIT'                  => 'OVER_QUERY_LIMIT_FOR_GOOGLE_API_KEY',
        'GOOGLE_API_NULL_RESPONSE'          => 'GOOGLE_DISTANCE_API_RETURNS_NULL',
        'INVALID_PARAMETERS'                => 'INVALID_PARAMETERS',
        'NOT_FOUND'                         => 'GOOGLE_API_GEOCODING_FOR_ORIGIN_OR_DESTINATION_CANNOT_PAIRED',
        'ZERO_RESULTS'                      => 'GOOGLE_API_DOESNOT_FOUND_ANY_ROUTES_FOR_GIVEN_VALUES',
    ];

    /**
     * Provided translated message if key is provided, otherwise provided whole array of
     * key->translated_message pairs
     *
     * @param string|null $key
     *
     * @return array|string|null
     */
    public function getMessage($key = null)
    {
        $messageList = self::$messages;

        if (null === $key) {
            return $messageList;
        }

        return isset($messageList[$key]) ? $messageList[$key] : null;
    }
}
