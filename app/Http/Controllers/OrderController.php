<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderListRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Response\Response;
use App\Http\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * @var \App\Http\Services\OrderService
     */
    protected $orderService;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param \App\Http\Services\OrderService $orderService
     * @param Response                 $response
     */
    public function __construct(OrderService $orderService, Response $response)
    {
        $this->orderService = $orderService;
        $this->response     = $response;
    }

    /**
     * Function to fetch the list of orders, $page and $limit variables in params
     * In case of invalid request parameters send proper message with 406 status code
     *
     * @param OrderListRequest $request
     *
     * @return JsonResponse
     */
    public function index(OrderListRequest $request)
    {
        try {
            $page  = (int) $request->get('page', 1);
            $limit = (int) $request->get('limit', 1);

            $ordersList = $this->orderService->getList($page, $limit);

            if (empty($ordersList)) {
                return $this->response->sendResponseAsError(
                    'NO_DATA_FOUND',
                    JsonResponse::HTTP_NO_CONTENT
                );
            }

            $orders = array();
            foreach ($ordersList as $orderItem) {
                $orders[] = $this->response->formatOrderAsResponse($orderItem);
            }
            //success case
            return $this->response->setSuccessResponse($orders);
        } catch (\Exception $exception) {
            return $this->response->sendResponseAsError(
                $exception->getMessage(),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param OrderCreateRequest $request
     *
     * @return JsonResponse
     */
    public function create(OrderCreateRequest $request)
    {
        try {
            $orderCreationRes = $this->orderService->create($request);
            if ($orderCreationRes) {
                $formattedResponse = $this->response->formatOrderAsResponse($orderCreationRes);
                return $this->response->setSuccessResponse($formattedResponse);
            } else {
                $messages  = $this->orderService->error;
                $errorCode = $this->orderService->errorCode;
                return $this->response->sendResponseAsError(
                    $messages,
                    $errorCode
                );
            }
        } catch (\Exception $e) {
            return $this->response->sendResponseAsError(
                $e->getMessage(),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Updates an order, providing valid orderID in params
     * If order is already being taken send response as 409 with proper message and status 417 when wrong order id
     *
     * @param OrderUpdateRequest $request
     * @param int                $id
     *
     * @return JsonResponse
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            //check if order exist for the given order id
            $this->orderService->getOrderById($id);

            if (false === $this->orderService->updateOrderStatus($id)) {
                return $this->response->sendResponseAsError(
                    'order_taken',
                    JsonResponse::HTTP_CONFLICT
                );
            }

            return $this->response->sendResponseAsSuccess(
                'success',
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $e) {
            return $this->response->sendResponseAsError(
                'invalid_id',
                JsonResponse::HTTP_EXPECTATION_FAILED
            );
        }
    }
}
