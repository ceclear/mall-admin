<?php

namespace App\Services;

use App\Models\UsersBalance;
use App\Models\UsersBalanceLog;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    /**
     * 余额改变
     * @param $uid
     * @param $amount
     * @param string $remark
     * @return bool
     * @throws \Exception
     */
    public static function change($uid, $amount, $remark = '', $isTotalIncome = false,$operation_log='')
    {
        DB::beginTransaction();

        try {
            $info = self::changeWithoutLog($uid, $amount, $isTotalIncome);

            self::writeLog($uid, $amount, $info['amount_before_change'], $remark,$operation_log);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 操作用户余额，不写日志
     * @param int $uid
     * @param float $amount
     * @return array
     * @throws \Exception
     */
    public static function changeWithoutLog(int $uid, float $amount, $isTotalIncome = false)
    {
        bcscale(8);
        DB::beginTransaction();
        //余额模型
        $balance = UsersBalance::getBalanceByUid($uid, "*", true);

        //改变前余额
        $amount_before_change = sprintf("%.8f", $balance->balance);
        $amount = sprintf("%.8f", $amount);
        //改变后余额
        $amount_after_change = bcadd($amount_before_change, $amount);

        //扣除操作或者扣除后余额小于0
        if (bccomp($amount, 0) == -1 && bccomp($amount_after_change, 0) == -1) {
            DB::rollBack();
            throw new \Exception('余额不足', 500);
        }

        //操作余额
        $balance->balance = $amount_after_change;

        //累计收入
        if ( $isTotalIncome && bccomp($amount, 0) == 1 ) {
            $balance->total_income = bcadd($balance->total_income, $amount);
        }
        if ( !$balance->save() ) {
            DB::rollBack();
            throw new \Exception('数据写入失败', 500);
        }

        DB::commit();
        return [
            'amount_before_change' => $amount_before_change,
            'amount_after_change' => $amount_after_change
        ];
    }

    /**
     * 写入日志
     * @param int $uid
     * @param float $amount
     * @param float $amountBeforeChange
     * @param string $remark
     * @return mixed
     */
    public static function writeLog(
        int $uid,
        float $amount,
        float $amountBeforeChange,
        string $remark,
        $operation_log=''
    ) {
        //写入日志
        $balancesLogs = new UsersBalanceLog();

        $balancesLogs->uid = $uid;
        $balancesLogs->amount = $amount;
        $balancesLogs->amount_before_change = $amountBeforeChange;
        $balancesLogs->remark = $remark;
        $balancesLogs->operation_log = $operation_log;
        $balancesLogs->save();

        return $balancesLogs->id;
    }
}
