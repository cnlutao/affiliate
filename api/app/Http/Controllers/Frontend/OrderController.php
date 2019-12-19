<?php
/**
 *
 * Author: zhaobin
 * Date: 2019-11-07
 * Time: 00:02
 */

namespace App\Http\Controllers\Frontend;


use App\Enum\OrderEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderListPost;
use App\Services\Frontend\OrderService;
use App\Services\Frontend\UserService;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * 列表
     * Author: zhaobin
     * Date: 2019-11-13
     *
     * @param OrderListPost $post
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function actionList(OrderListPost $post)
    {
        $totalRows = 0;
        $list = OrderService::singleton()->findListByPage($post, $totalRows);

        if (count($list)) {
            $idList = array_column($list, 'id');
            $tmpList = OrderService::singleton()->findGoodsListById( $idList);
            $orderGoodsList = [];
            foreach ($tmpList as $goods) {
                $orderGoodsList[$goods->order_id][] = $goods;
            }
            foreach ( $list as &$item ) {
                if (isset( $orderGoodsList[$item->id])) {
                    $item->setAttribute( 'goods_list', $orderGoodsList[ $item->id ] );
                } else {
                    $item->setAttribute( 'goods_list', [] );
                }
                $item->statusText = OrderEnum::STATUS_TEXT_LIST[ $item->order_status ] ?? '';
                $item->commissionStatusText = OrderEnum::COMMISSION_STATUS_TEXT_LIST[ $item->commission_status ] ?? '';
            }
        }

        return $this->jsonSuccess([
            'list' => $list,
            'page' => $post->page,
            'perPage' => $post->perPage,
            'totalRows' => $totalRows
        ]);
    }

}