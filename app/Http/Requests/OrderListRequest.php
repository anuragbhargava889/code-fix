<?php

namespace App\Http\Requests;

class OrderListRequest extends AbstractFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page'  => [
                'required',
                'int',
                'min:1',
            ],
            'limit' => [
                'required',
                'int',
                'min:1',
            ],
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'page.required'  => 'REQUEST_PARAMETER_MISSING',
            'page.integer'   => 'INVALID_PARAMETER_TYPE',
            'page.min'       => 'INVALID_PARAMETERS',
            'limit.required' => 'REQUEST_PARAMETER_MISSING',
            'limit.integer'  => 'INVALID_PARAMETER_TYPE',
            'limit.min'      => 'INVALID_PARAMETERS',
        ];
    }
}
