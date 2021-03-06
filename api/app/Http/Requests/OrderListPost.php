<?php

namespace App\Http\Requests;

use App\Enum\OrderEnum;
use App\Http\Requests\BasePageListPost;

/**
 * Class OrderListPost
 * @package App\Http\Requests
 *
 * @property $order_sn string
 * @property $status int
 */
class OrderListPost extends BasePageListPost
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'nullable|integer|in:'.implode(',', [
                OrderEnum::STATUS_PENDING,
                OrderEnum::STATUS_CANCELLED,
                OrderEnum::STATUS_PAYED,
                OrderEnum::STATUS_DELIVERED,
            ]),
            'order_sn' => 'nullable|min:1|max:64',
        ];
    }

    public function attributes()
    {
        return [
            'order_sn' => '订单号',
            'status' => '订单状态',
        ];
    }
}
