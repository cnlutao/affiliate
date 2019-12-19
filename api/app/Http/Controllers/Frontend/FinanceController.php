<?php
/**
 *
 * Author: zhaobin
 * Date: 2019-11-07
 * Time: 00:02
 */

namespace App\Http\Controllers\Frontend;


use App\Enum\CodeEnum;
use App\Enum\CommonEnum;
use App\Enum\OrderEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BasePageListPost;
use App\Http\Requests\OrderListPost;
use App\Http\Requests\WithdrawCreatePost;
use App\Services\Frontend\FinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    /**
     * 收到明细列表
     * Author: zhaobin
     * Date: 2019-11-13
     *
     * @param BasePageListPost $post
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function actionTradeList(BasePageListPost $post)
    {
        $userId = Auth::id();
        $totalRows = 0;
        $list = FinanceService::singleton()->findTradeListByPage($userId, $post, $totalRows);

        if (count($list)) {
            foreach ( $list as &$item ) {
                $item->typeText = CommonEnum::FINANCE_TRADE_TYPE_TEXT_LIST[ $item->type ] ?? '';
            }
        }

        return $this->jsonSuccess([
            'list' => $list,
            'page' => $post->page,
            'perPage' => $post->perPage,
            'totalRows' => $totalRows
        ]);
    }

    /**
     * 查余额
     * Author: zhaobin
     * Date: 2019-11-24
     */
    public function actionBalance()
    {
        $userId = Auth::id();
        $balanceModel = FinanceService::singleton()->findBalance( $userId);

        $balance = $balanceModel ? $balanceModel->computeBalance() : 0;

        return $this->jsonSuccess([
            //总余额（含冻结）
            'balance' => $balanceModel ? bcadd($balance, $balanceModel->frozen, 2) : 0,
            //冻结中金额
            'frozen' => $balanceModel ? $balanceModel->frozen : 0,
            //可提金额
            'available' => $balance,
        ]);
    }

    /**
     * 提现申请
     * Author: zhaobin
     * Date: 2019-11-25
     */
    public function actionWithdrawCreate(WithdrawCreatePost $post)
    {
        $userId = Auth::id();

        $balanceModel = FinanceService::singleton()->findBalance( $userId);
        if (bccomp($balanceModel->balance, $post->amount) < 0) {
            //余额不足
            return $this->jsonFail( CodeEnum::FINANCE_NOT_ENOUGH_BALANCE, '余额不足');
        }
        $ret = FinanceService::singleton()->createWithdraw( $userId, $post);
        if (!$ret) {
            return $this->jsonFail( CodeEnum::BASE_SERVER_ERROR, __('base.server_error'));
        }
        return $this->jsonSuccess();
    }

    /**
     * 提现列表
     * Author: zhaobin
     * Date: 2019-11-25
     *
     * @param BasePageListPost $post
     * @return JsonResponse
     */
    public function actionWithdrawList(BasePageListPost $post)
    {
        $userId = Auth::id();
        $totalRows = 0;
        $list = FinanceService::singleton()->findWithdrawListByUser( $userId, $post, $totalRows);
        if (count($list)) {
            foreach ($list as $item) {
                $item->setAttribute( 'statusText', CommonEnum::WITHDRAW_STATUS_TEXT_LIST[$item->status]);
                $item->setAttribute( 'wayText', CommonEnum::WITHDRAW_WAY_TEXT_LIST[$item->way]);
            }
        }

        return $this->jsonSuccess([
            'list' => $list,
            'totalRows' => $totalRows
        ]);
    }

}