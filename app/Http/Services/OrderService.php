<?php

namespace App\Http\Services;

use App\Http\Models\Distance;
use App\Http\Models\Order;
use App\Validators\RouteCoordinatesValidator;
use Illuminate\Http\JsonResponse;
use App\Helpers\DistanceHelper;
use App\Http\Requests\OrderCreateRequest;

class OrderService
{
    /**
     * @var null|string
     */
    public $error = null;

    /**
     * @var int
     */
    public $errorCode;

    /**
     * @var routeCoordinatesValidator
     */
    protected $routeCoordinatesValidator;

    /**
     * @var DistanceHelper
     */
    protected $distanceHelper;

    /**
     * @param RouteCoordinatesValidator $routeCoordinatesValidator
     * @param DistanceHelper    $distanceHelper
     */
    public function __construct(
        RouteCoordinatesValidator $routeCoordinatesValidator,
        DistanceHelper $distanceHelper
    ) {

        $this->routeCoordinatesValidator = $routeCoordinatesValidator;
        $this->distanceHelper            = $distanceHelper;
    }

    /**
     * Create a order based on geo location provided in requestData param
     *
     * @param OrderCreateRequest $requestData
     *
     * @return Order|false
     */
    public function create($requestData)
    {
        $sourceLatitude       = $requestData->source[0];
        $sourceLongitude      = $requestData->source[1];
        $destinationLatitude  = $requestData->destination[0];
        $destinationLongitude = $requestData->destination[1];

        //here we need to validate the source and dest lat long
        $validateSourceDestLatLong = $this->routeCoordinatesValidator
            ->validate(
                $sourceLatitude,
                $sourceLongitude,
                $destinationLatitude,
                $destinationLongitude
            );

        if (!$validateSourceDestLatLong) {
            $this->error     = $this->routeCoordinatesValidator->getError();
            $this->errorCode = JsonResponse::HTTP_NOT_ACCEPTABLE;
            return false;
        }

        $distance = $this->getDistance(
            $sourceLatitude,
            $sourceLongitude,
            $destinationLatitude,
            $destinationLongitude
        );

        if (!$distance instanceof \App\Http\Models\Distance) {
            $this->error     = $distance;
            $this->errorCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
            return false;
        }

        //Create new record
        $order                 = new Order();
        $order->status         = Order::UNASSIGNED_ORDER_STATUS;
        $order->distance_id    = $distance->id;
        $order->distance_value = $distance->distance;
        $order->save();

        return $order;
    }

    /**
     * @param float $sourceLatitude
     * @param float $sourceLongitude
     * @param float $destinationLatitude
     * @param float $destinationLongitude
     *
     * @return int
     */
    public function getDistance(
        $sourceLatitude,
        $sourceLongitude,
        $destinationLatitude,
        $destinationLongitude
    ) {


        $distanceObj = new Distance;

        //check if distance exist use that
        $distanceExist = $distanceObj->checkIfDistanceExist(
            $sourceLatitude,
            $sourceLongitude,
            $destinationLatitude,
            $destinationLongitude
        );
        if (!empty($distanceExist)) {
            return $distanceExist;
        }

        //no existing object found so create new
        $source          = $sourceLatitude.",".$sourceLongitude;
        $destination     = $destinationLatitude.",".$destinationLongitude;
        $distanceBetween = $this->distanceHelper->getDistance(
            $source,
            $destination
        );

        if (!is_int($distanceBetween)) {
            return $distanceBetween;
        }

        return $distanceObj->saveDistance(
            $sourceLatitude,
            $sourceLongitude,
            $destinationLatitude,
            $destinationLongitude,
            $distanceBetween
        );
    }

    /**
     * Fetches list of order in system using given limit and page variable
     *
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getList($page, $limit)
    {
        $page   = (int) $page;
        $limit  = (int) $limit;
        $orders = [];

        if ($page > 0 && $limit > 0) {
            $skip   = ($page - 1) * $limit;
            $orders = (new Order())->skip($skip)->take($limit)->get();
        }

        return $orders;
    }

    /**
     * Fetches Order model based on primary key provided
     *
     * @param int $id
     *
     * @return Order
     */
    public function getOrderById($id)
    {
        $order = new Order();
        return $order->getOrderById($id);
    }

    /**
     * Mark an order as TAKEN, if not already
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function updateOrderStatus($orderId)
    {
        $order = new Order();
        return $order->updateOrderStatus($orderId);
    }
}
