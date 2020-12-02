<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redis;

class SaleRank extends Model
{
    public function paginate()
    {
        $ret = $this->getTop();
        $ret = static::hydrate($ret);
        $paginator = new LengthAwarePaginator($ret, 200, 200);
        //$paginator->
        $paginator->setPath(url()->current());

        return $paginator;
    }

    public function getTop()
    {
        $saleRank = Redis::zrevrange("sale_list_top", 0, -1, "WITHSCORES");
        $uid = array_keys($saleRank);
        $users = UsersInfo::whereIn("id", $uid)->select(["phone", "nickname", "id"])->get()->keyBy("id")->toArray();
        $ret = [];
        $level = 1;
        foreach ($saleRank as $key => $item) {
            if (isset($users[$key])) {
                $ret[] = [
                    "id" => $users[$key]["id"],
                    "nickname" => $users[$key]["nickname"],
                    "phone" => $users[$key]["phone"],
                    "sale_price" => $item,
                    'level' => $level,
                    'reward' => SalesTop::getReward($level, $item)
                ];
            } else {
                $ret[] = [
                    "id" => "-",
                    "nickname" => $key . "【假数据】",
                    "phone" => $key,
                    "sale_price" => $item,
                    'level' => $level,
                    'reward' => SalesTop::getReward($level, $item)
                ];
            }
            $level++;
        }
        return $ret;
    }
}
