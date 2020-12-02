<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserCards
 *
 * @property int $id
 * @property int $uid 用户id
 * @property string $id_card 身份证号
 * @property string $name 姓名
 * @property int $verify 验证状态，0未验证 1验证成功 2验证失败
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $card_no 银行卡号
 * @property string $bank_name 所属银行
 * @property string $bank_code 银行简称
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards whereBankCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards whereCardNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards whereIdCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCards whereVerify($value)
 * @mixin \Eloquent
 */
class UserCards extends Model
{

    protected $fillable = [
        'id',
        'uid',
        'id_card',
        'name',
        'verify',
        'created_at',
        'updated_at',
        'card_no',
        'bank_name',
        'bank_code',
    ];

    protected $connection = "mysql_lianxin";
    protected $table = "user_cards";

    /**添加身份信息
     * @param $uid
     * @param $id_card
     * @param $name
     * @param $verify
     * @param null $card_no
     * @param null $bank_name
     * @param null $bank_code
     */
    public function add($uid,$id_card,$name,$verify,$card_no="",$bank_name="",$bank_code="")
    {
        $this->uid = $uid;
        $this->id_card = strtoupper($id_card);
        $this->name = $name;
        $this->verify = $verify;
        $this->card_no = $card_no;
        $this->bank_name = $bank_name;
        $this->bank_code = $bank_code??"";

        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        // 添加匿名全局作用域
        static::addGlobalScope('idCard',function (Builder $query){
            $bindings = $query->getQuery()->getBindings();

            // 如果查询条件中有[id_card, name], 则对字段加密
            foreach ($query->getQuery()->wheres as $index => $item){

                // 查询构造器方式
                if (strtolower($item['type']) == 'basic' && in_array($item['column'], ['id_card', 'name'])){
                    $bindings[$index] = static::encrypt($item['value']);
                    $query->setBindings($bindings);
                }

                // 原生SQL语句
                if (strtolower($item['type']) == 'raw' ){
                    if (strpos($item['sql'], 'name') !== false ||
                        strpos($item['sql'], 'id_card') !== false){
                        preg_match("/\'(\S+)\'/", $item['sql'], $arg);
                        $aes = static::encrypt($arg[1]);
                        $item['sql'] = preg_replace("/\'(\S+)\'/", "'$aes'", $item['sql']);
                        $query->getQuery()->wheres[$index] = $item;
                    }
                }
            }
        });

        // 新数据同步到新表
        static::created(function ($model){
            $count = UserCardNew::orWhere('uid', $model->uid)
                ->orWhere('id_card', $model->id_card)
                ->count();

            if ($count == 0){
                UserCardNew::create([
                    'uid' => $model->uid,
                    'id_card' => $model->id_card,
                    'name' => $model->name,
                    'verify' => $model->verify,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                    'card_no' => $model->card_no,
                    'bank_name' => $model->bank_name,
                    'bank_code' => $model->bank_code,
                ]);
            }
        });
    }

    /**
     * 字段加密
     * @param $value
     *
     * @return string
     */
    public static function encrypt($value){
        $method = 'AES-128-ECB';
        $key = env('AES_PASSWORD_KEY', 'key');
        return strtoupper(bin2hex(openssl_encrypt($value, $method, $key, OPENSSL_RAW_DATA)));
    }

    /**
     * 字段解密
     * @param $value
     *
     * @return string
     */
    public static function decrypt($value){
        if (strlen($value) < 20){
            return $value;
        }
        $method = 'AES-128-ECB';
        $key = env('AES_PASSWORD_KEY', 'key');
        return openssl_decrypt(hex2bin(strtolower($value)), $method, $key, OPENSSL_RAW_DATA);
    }

    /**
     * 身份证号码加密入库
     * @param $value
     *
     * @return string
     */
    public function getIdCardAttribute($value){
        return static::decrypt($value);
    }

    /**
     * 身份证号码解密
     * @param $value
     */
    public function setIdCardAttribute($value){
        $this->attributes['id_card'] = static::encrypt($value);
    }

    /**
     * 姓名加密入库
     * @param $value
     *
     * @return string
     */
    public function getNameAttribute($value){
        return static::decrypt($value);
    }

    /**
     * 姓名解密
     * @param $value
     */
    public function setNameAttribute($value){
        $this->attributes['name'] = static::encrypt($value);
    }
}
