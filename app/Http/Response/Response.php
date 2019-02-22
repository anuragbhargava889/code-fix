<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;
use App\Http\Models\Order;
use App\Helpers\ResponseMessageHelper;

class Response
{
    /**
     * @var \App\Helpers\ResponseMessageHelper
     */
    protected $responseMsgHelper;

    /**
     * @param \App\Helpers\ResponseMessageHelper $responseMsgHelper
     */
    public function __construct(ResponseMessageHelper $responseMsgHelper)
    {
        $this->responseMsgHelper = $responseMsgHelper;
    }

    /**
     * @param string  $message
     * @param int  $responseCode
     * @param boolean $translateMessage
     *
     * @return JsonResponse
     */
    public function sendResponseAsError(
        $message,
        $responseCode = JsonResponse::HTTP_BAD_REQUEST,
        $translateMessage = true
    ) {

        if (true === $translateMessage) {
            $message = $this->responseMsgHelper->getMessage($message) ?: $message;
        }

        $response = ['error' => $message];

        return response()->json($response, $responseCode);
    }

    /**
     * @param string  $message
     * @param int  $responseCode
     * @param boolean $translateMessage
     *
     * @return JsonResponse
     */
    public function sendResponseAsSuccess($message, $responseCode = JsonResponse::HTTP_OK, $translateMessage = true)
    {
        if (true === $translateMessage) {
            $message = $this->responseMsgHelper->getMessage($message) ?: $message;
        }

        $response = ['status' => $message];

        return response()->json($response, $responseCode);
    }

    /**
     * @param $response
     *
     * @return JsonResponse
     */
    public function setSuccessResponse($response)
    {
        return response()->json($response, JsonResponse::HTTP_OK);
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public function formatOrderAsResponse(Order $order)
    {
        return [
            'id' => $order->id,
            'distance' => $order->getDistanceValue(),
            'status' => $order->status
        ];
    }
}
