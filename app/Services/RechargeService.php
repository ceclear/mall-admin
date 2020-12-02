<?php

namespace App\Services;

use App\Models\PayLogs;
use App\Models\RechargeOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class RechargeService
{

    private $appkey;

    private $oilAppKey;

    private $openid;

    private $telCheckUrl = 'http://op.juhe.cn/ofpay/mobile/telcheck';

    private $telQueryUrl = 'http://op.juhe.cn/ofpay/mobile/telquery';

    private $submitUrl = 'http://op.juhe.cn/ofpay/mobile/onlineorder';

    private $staUrl = 'http://op.juhe.cn/ofpay/mobile/ordersta';

    public function __construct()
    {
        $this->appkey = env('JUHE_APP_KEY');
        $this->openid = env('JUHE_OPENID');
    }

    /**
     * 根据手机号码及面额查询是否支持充值
     * @param string $mobile [手机号码]
     * @param int $pervalue [充值金额]
     * @return  boolean
     */
    public function telcheck($mobile, $pervalue)
    {
        $params = 'key=' . $this->appkey . '&phoneno=' . $mobile . '&cardnum=' . $pervalue;
        $content = $this->juhecurl($this->telCheckUrl, $params);
        $result = $this->_returnArray($content);
        if ($result['error_code'] == '0') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据手机号码和面额获取商品信息
     * @param string $mobile [手机号码]
     * @param int $pervalue [充值金额]
     * @return  array
     */
    public function telquery($mobile, $pervalue)
    {
        $params = 'key=' . $this->appkey . '&phoneno=' . $mobile . '&cardnum=' . $pervalue;
        $content = $this->juhecurl($this->telQueryUrl, $params);
        return $this->_returnArray($content);
    }

    /**
     * 提交话费充值
     * @param  [string] $mobile   [手机号码]
     * @param  [int] $pervalue [充值面额]
     * @param  [string] $orderid  [自定义单号]
     * @return  [array]
     */
    public function telcz($mobile, $pervalue, $orderid)
    {
        $sign = md5($this->openid . $this->appkey . $mobile . $pervalue . $orderid);//校验值计算
        $params = array(
            'key' => $this->appkey,
            'phoneno' => $mobile,
            'cardnum' => $pervalue,
            'orderid' => $orderid,
            'sign' => $sign
        );
        $content = $this->juhecurl($this->submitUrl, $params, 1);
        return $this->_returnArray($content);
    }

    /**
     * 查询订单的充值状态
     * @param  [string] $orderid [自定义单号]
     * @return  [array]
     */
    public function sta($orderid)
    {
        $params = 'key=' . $this->appkey . '&orderid=' . $orderid;
        $content = $this->juhecurl($this->staUrl, $params);
        return $this->_returnArray($content);
    }

    /**
     * 将JSON内容转为数据，并返回
     * @param string $content [内容]
     * @return array
     */
    public function _returnArray($content)
    {
        return json_decode($content, true);
    }

    /**
     * 请求接口返回内容
     * @param string $url [请求的URL地址]
     * @param string $params [请求的参数]
     * @param int $ipost [是否采用POST形式]
     * @return  string
     */
    public function juhecurl($url, $params = false, $ispost = 0)
    {
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

    /**
     * 话费充值
     *
     * @param $sn
     * @param $uid
     * @param $phone
     * @param $amount
     * @param $payAmount
     * @param int $payment
     * @param int $useTicket
     * @param array $tickets
     * @param int $ticketAmount
     * @return bool
     */
    public function rechargeOrder($sn, $uid, $phone, $amount, $payAmount, $payment = 2, $useTicket = 0, array $tickets, $ticketAmount = 0)
    {
        DB::beginTransaction();
        try {
            $recharge = new RechargeOrder();
            $recharge->order_sn = $sn;
            $recharge->uid = $uid;
            $recharge->phone = $phone;
            $recharge->amount = $amount;
            $recharge->use_ticket = $useTicket;
            $recharge->ticket_amount = $ticketAmount;
            $recharge->pay_amount = $payAmount;
            $recharge->status = bccomp($payAmount, 0, 2) != 1 ? 1 : 0;
            $recharge->payment = $payment;
            $recharge->tickets = json_encode($tickets);
            $recharge->save();


            $payLogs = new PayLogs();
            $payLogs->order_sn = $sn;
            $payLogs->uid = $uid;
            $payLogs->pay_type = 1;
            $payLogs->payment = $payment;
            $payLogs->pay_amount = $payAmount;
            $payLogs->status = bccomp($payAmount, 0, 2) != 1 ? 1 : 0;
            $payLogs->remark = $phone . "话费充值" . $amount . "元";
            $payLogs->save();

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * 支付成功处理
     * @param $orderSn
     * @return bool
     */
    public function rechargePaySuccess($payLogs)
    {
        DB::beginTransaction();
        //充值订单
        $recharge = RechargeOrder::where("order_sn", $payLogs->order_sn)->lockForUpdate()->where("status", 0)->first();
        try {
            if (is_null($recharge)) {
                DB::rollBack();
                return false;
            }

            //充值订单修改为
            $recharge->status = 1;
            $recharge->save();

            //支付记录修改为支付成功
            $payLogs->status = 1;
            $payLogs->save();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }

        //话费充值
        $telRechargeRes = $this->telcz($recharge->phone, $recharge->amount, $payLogs->order_sn);

        if ($telRechargeRes['error_code'] == '0') {
            Log::debug("充值成功，请等待到账。(订单号：{$telRechargeRes['result']['sporder_id']})", []);
        } else {
            Log::debug("充值失败，错误原因：" . $telRechargeRes['reason'] . "，请截图联系客服", []);
        }
    }

    /**
     * 充值失败的订单修复
     */
    public function rechargeFail()
    {
        $recharges = Recharge::where('status', 7)
            ->where("reason", "208513:订单无效/受理失败")
            ->get();

        foreach ($recharges as $recharge) {

//            $recharge->status = 2;
//            $recharge->reason = "充值失败重新提交";
//            $recharge->save();
//
//            //话费充值
//            $telRechargeRes = $this->telcz($recharge->phone,$recharge->amount,"fr".$recharge->order_sn);
//
//            if($telRechargeRes['error_code'] =='0'){
//                echo "订单号：".$recharge->order_sn."重新提交成功,充值成功，请等待到账". "<br>";
//            }else {
//                echo "订单号：" . $recharge->order_sn . "充值失败，错误原因：" . $telRechargeRes['reason']. "<br>";
//            }

            echo $recharge->order_sn . "<br>";

            $RechargeService = new RechargeService();
            $orderStatusRes = $RechargeService->sta("fr" . $recharge->order_sn);
            if ($orderStatusRes['error_code'] == '0') {
                $recharge->reason = "{$orderStatusRes['error_code']}:{$orderStatusRes['reason']}";
                if ($orderStatusRes['result']['game_state'] == 9) {
                    $recharge->status = 4;
                    $need_push = $recharge->save();
                } elseif ($orderStatusRes['result']['game_state'] == 1) {
                    $recharge->status = 3;
                    $need_push = $recharge->save();
                }
            } else if ($orderStatusRes['error_code'] != '10014') {
                $recharge->reason = "{$orderStatusRes['error_code']}:{$orderStatusRes['reason']}";
                $recharge->status = 4;
                $need_push = $recharge->save();
            }
        }
    }

    /**
     * 更新订单状态
     * @param $orderid
     * @return array
     */
    public function updateOilStatus($orderid)
    {
        $url = "http://op.juhe.cn/ofpay/sinopec/ordersta";
        $params = array(
            "orderid" => $orderid,//商家订单号，8-32位字母数字组合
            "key" => $this->oilAppKey,//应用APPKEY(应用详细页查询)
        );
        $paramstring = http_build_query($params);
        $content = $this->juhecurl($url, $paramstring);
        return $this->_returnArray($content);
    }
}
