<?php

namespace App\Http\Middleware;

use App\Http\Response\Response;
use Closure;
use Illuminate\Http\JsonResponse;

class CheckValidId
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $id = $request->id;

        if (! is_numeric($id)) {
            return $this->response->sendResponseAsError(
                'invalid_id',
                JsonResponse::HTTP_NOT_ACCEPTABLE
            );
        }

        return $next($request);
    }
}
