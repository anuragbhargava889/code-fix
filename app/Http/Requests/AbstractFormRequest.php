<?php

namespace App\Http\Requests;

use App\Http\Response\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class AbstractFormRequest extends FormRequest
{
    /**
     * @var Response
     */
    protected $responseHelper;

    /**
     * @param Response $responseHelper
     */
    public function __construct(Response $responseHelper)
    {
        parent::__construct();
        $this->responseHelper = $responseHelper;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $firstError = ! empty($validator->errors()->all()[0]) ? $validator->errors()->all()[0] : 'INVALID_PARAMETERS';

        throw new HttpResponseException(
            $this->responseHelper->sendResponseAsError(
                $firstError,
                JsonResponse::HTTP_NOT_ACCEPTABLE
            )
        );
    }
}
