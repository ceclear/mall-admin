<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get("goods-cat/getCat", "GoodsCatController@getCat");
    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->get('load-department', 'DepartmentController@apiAjaxById');
//    $router->resource('fee-sys-rate',FeeSysRateController::class);//返利设置
    $router->get('api/fee-sys-rate', 'FeeSysRateController@changeStatus');//返利设置状态修改
    $router->get('fee-cfg/create-cfg', 'FeeCfgController@createFeeCfg');
//    $router->resource('fee-cfg',FeeCfgController::class);//佣金设置
    $router->get('goods/calculate', 'GoodsController@calculate');//预采集
    $router->resource("goods-cat", GoodsCatController::class);
    $router->resource("goods", GoodsController::class);

    $router->post("goods/collection", "GoodsController@collection");
    $router->post("activity-invite/updatePrizeNum", "ActivityInvitationUserPlController@updatePrizeNum");


    $router->get('load-department', 'DepartmentController@apiAjaxById');
    $router->get('api/fee-sys-rate', 'FeeSysRateController@changeStatus');//返利设置状态修改

    $router->get('ad-position-status', 'AdPositionController@changeStatus');//广告位状态修改
    $router->get('ad-status', 'AdvertController@changeStatus');//广告状态修改
    $router->get('point-status', 'PointController@changeStatus');//场次状态修改
    $router->get('order-statics', 'OrderController@statics');//订单统计
    $router->get('order-statics-data', 'OrderController@staticsData');//订单统计

    $router->group(['prefix' => 'fee-cfg'], function ($router) {
        $router->get('create-cfg-partner', 'FeeCfgController@createPartnerFeeCfg');//合伙人佣金设置
        $router->post('partner-submit', 'FeeCfgController@partnerFeeCfgSubmit');//合伙人佣金提交
        $router->get('create-cfg-commander', 'FeeCfgController@createCommanderFeeCfg');//团长佣金设置
        $router->post('commander-submit', 'FeeCfgController@commanderFeeCfgSubmit');//团长，高级团长佣金提交
        $router->get('fee-cfg-status', 'FeeCfgController@changeStatus');//佣金设置状态修改
    });
    $router->get('activity-status', 'ActivityController@changeStatus');//活动状态修改
    $router->get('goods-people-status', 'GoodsPeopleController@changeStatus');//活动状态修改
    $router->get('king-status', 'KingController@changeStatus');//活动状态修改

    $router->post('goods-nine/nine-goods-collection', 'GoodsNineController@collectionNineGoods');//99包邮数据抓取
    $router->get('users/parentGrid', 'UserController@parentGrid');//父级数量
    $router->get('users/show-logs', 'UserController@showLogs');//查看余额日志
    $router->get('order/setRefundTag', "OrderController@setRefundTag");//标记订单维权
    $router->resources([
        'fee-sys-rate'=> FeeSysRateController::class,//返利设置
        'fee-cfg'=> FeeCfgController::class,//佣金设置
        'ad-position'=> AdPositionController::class,//广告位置
        'advert'=> AdvertController::class,//广告
        'point'=> PointController::class,//场次
        'point-goods'=> PointGoodsController::class,//秒杀商品
        'goods-nine'=> GoodsNineController::class,//9.9商品
        'android'=> AndroidVersionController::class,//安卓版本管理
        'users'=> UserController::class,//会员管理
        'tb-users'=> TbRelationController::class,//淘宝绑定管理
        'rebate'=> RebateController::class,//商城返利
        'reject'=> RejectManageController::class,//上架管理
        'app-settings'=> SettingsController::class,//设置
        'order'=>OrderController::class,//订单
        'goods-people'=>GoodsPeopleController::class,//爆款管理
        'activity'=>ActivityController::class,//活动管理
        'king'=>KingController::class,//金刚区
        'errror-logs'=>ErrorLogsController::class,//日志
        'activity-invite'=>ActivityInvitationUserPlController::class,//活动邀请
        'user-coupon'=>UserCouponsController::class,//活动邀请
    ]);

    //好省圈
    $router->group(['prefix' => 'circle'], function ($router) {
        $router->get('accounts/account', 'CircleAccountController@account');//账号获取接口
        $router->resource('accounts', CircleAccountController::class);//好省圈账户
        $router->resource('areas', CircleAreaController::class);//好省圈分区
        $router->resource('tags', CircleTagController::class);//好省圈标签
        $router->resource('select', CircleSelectController::class);//链信优选
        $router->resource('material', CircleMaterialController::class);//营销素材
        $router->resource('school', CircleSchoolController::class);//链信优选
    });

    $router->group(['prefix' => 'recharge'], function ($router) {
        $router->resource('order', RechargeOrderController::class);
        $router->post('order/refund', 'RechargeOrderController@refund');
    });

    $router->get('nine-goods-top', 'GoodsNineController@changeStatus');//顶置修改
    $router->get('goods-top', 'GoodsController@changeTop');//顶置修改

    #品牌闪购管理
    $router->group(['prefix' => 'brand-f-b'], function ($router) {
        $router->resource('special-type', SpecialTyperController::class);
        $router->resource('special-brand', SpecialBrandController::class);
        $router->get('change-top', 'SpecialBrandController@changeStatus');//顶置修改
    });
    $router->get('brand-f-b/special-brands/get-goods', 'SpecialBrandController@getGoods');


    $router->resource('share-posters', SharePostersController::class);

    //提现管理
    $router->group(['prefix' => 'withdrawal'], function ($router) {
        $router->get('setting', 'WithdrawalLogController@setting');//提现记录
        $router->resource('log', WithdrawalLogController::class);//提现记录
        $router->post('log/change', 'WithdrawalLogController@change');//改变状态
        $router->resource('amount', WithdrawalAmountController::class);
        $router->resource('tax', WithdrawalTaxController::class);//扣税记录
    });

    //弹框管理
    $router->resource('alerts', AlertController::class);

    //推送管理
    $router->group(['prefix' => 'push'], function ($router) {
        $router->resource('type', PushTypeController::class);//推送类型
        $router->resource('list', PushListController::class);//推送列表
        $router->post('now', 'PushListController@now');//立即推送
    });

    $router->get('userBenefitTotal/settlement', "UserBenefitTotalController@settlement");//提现记录
    $router->resource('userBenefitTotal', UserBenefitTotalController::class);//提现记录u
    $router->resource('users-benefits', UserBenefitController::class);
    $router->resource('ubt', UbtController::class);

    $router->resource('free-activities', FreeActivityController::class);
    $router->resource('new-people-free-logs', NewPoepleLogsController::class);


    $router->get('execQueue/restExec', "ExecQueueController@restExec");//提现记录
    $router->resource('execQueue', ExecQueueController::class);//提现记录

    //提现管理
    $router->group(['prefix' => 'dbInfo'], function ($router) {
        $router->get('processlist', 'DbInfoController@processlist');
    });

    $router->group(["prefix" => "activitySimConfigs"], function($router){
        $router->get("invitation", "ActivitySimConfigController@invitation");
        $router->get("saleRank", "ActivitySimConfigController@saleRank");
        $router->post("saleRank/refund", "ActivitySimConfigController@rechargeRefund");
        $router->any("sale", "ActivitySimConfigController@sale");
        $router->any("setInvitation", "ActivitySimConfigController@setInvitation");
        $router->any("invitationLog", "ActivitySimConfigController@invitationLog");
    });

    $router->resource('user-coupons', UserCouponsController::class);
});
