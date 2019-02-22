<?php

namespace App\Http\Requests;

use App\Http\Models\Order;

class OrderUpdateRequest extends AbstractFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'status' => [
                'required',
                'string',
                function ($attr, $value, $fail) {
                    if ($value !== Order::ASSIGNED_ORDER_STATUS) {
                        $fail('status_is_invalid');
                    }
                },
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
            'status.required' => 'status_is_invalid',
            'status.string'   => 'status_is_invalid',
        ];
    }
}
