<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        ::-webkit-input-placeholder { /* WebKit browsers */
            color: red;
            font-size: 12px;
        }

        ::-moz-placeholder { /* Mozilla Firefox 19+ */
            color: red;
            font-size: 12px;
        }

        :-ms-input-placeholder { /* Internet Explorer 10+ */
            color: red;
            font-size: 12px;
        }

        .form-group {
            width: 20%;
            float: left;
        }

        .total-div {
            width: 100%;
            height: 40px;
            margin: 0px;
            background: red;
            line-height: 40px;
            text-align: center;
        }

        .total-div-width {
            width: 20%;
            margin-top: 50px;
        }

        .table-th {
            width: 25%;
            text-align: center;
        }

        .table-column {
            text-align: center;
        }

    </style>
</head>
<body>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">

                <!-- /.box-header -->
                <!-- form start -->
                <form id="post_form" method="post" accept-charset="UTF-8"
                      class="form-horizontal"
                      pjax-container="">

                    <div class="box-body">
                        <div class="fields-group">

                            <div class="form-group">
                                {{--                                <label class="col-sm-2 control-label">板块选择</label>--}}
                                <div class="col-sm-8">
                                    <select style="cursor: pointer" class="form-control " id="partition"
                                            name="partition">
                                        <option value="">请选择</option>
                                        @if(!empty($partition))
                                            @foreach($partition as $key=>$item)
                                                <option value="{{$key}}">{{$item}}</option>
                                            @endforeach
                                        @endif
                                    </select></div>
                            </div>
                            <div class="form-group">

                                <div class="col-sm-8">
                                    <select style="cursor: pointer" class="form-control " id="cat_id_one"
                                            name="cat_id_one">
                                        <option value="">请选择分类</option>
                                        @if(!empty($cat_parent))
                                            @foreach($cat_parent as $key=>$item)
                                                <option value="{{$key}}">{{$item}}</option>
                                            @endforeach
                                        @endif

                                    </select></div>
                            </div>
                            <div class="form-group">

                                <div class="col-sm-8">
                                    <select style="cursor: pointer" class="form-control " id="cat_id_two"
                                            name="cat_id_two">
                                        <option value="">请选择二级分类</option>
                                    </select></div>
                            </div>

                            <div class="form-group">
                                {{--                                <label class="col-sm-2 control-label">下单时间</label>--}}
                                <div class="col-sm-8" style="width: 80%">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control" id="start_time" placeholder="下单时间"
                                               name="start_time" value="">
                                        <span class="input-group-addon"
                                              style="border-left: 0; border-right: 0;">-</span>
                                        <input type="text" class="form-control" id="end_time" placeholder="下单时间"
                                               name="end_time" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {{--                                <label class="col-sm-2 control-label">下单时间</label>--}}
                                <div class="col-sm-8" style="width: 80%">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-addon">
                                            <i class="fa fa-pencil"></i>
                                        </div>
                                        <input type="text" class="form-control" id="goods_item_id" placeholder="商品ID"
                                               name="goods_item_id" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="width: 40px">

                                <div class="col-sm-8" style="width: 390px">
                                    <div class="input-group input-group-sm">
                                        <input type="button" class="btn btn-primary" id="search_btn" value="搜索">

                                    </div>
                                </div>
                            </div>

                            <div class="form-group total-div-width" style="margin-left: 0;margin-right: 0">
                                <div class="col-sm-8 total-div" style="background: rgb(149, 123, 190)">
                                    总成交订单数量：<span id="total_recharge">0</span>
                                </div>
                            </div>
                            <div class="form-group total-div-width" style="margin-left: 0;margin-right: 0">
                                <div class="col-sm-8 total-div" style="background: rgb(248, 215, 218)">
                                    总成交金额：<span id="total_money">0</span>
                                </div>
                            </div>
                            <div class="form-group total-div-width" style="margin-left: 0;margin-right: 0">
                                <div class="col-sm-8 total-div" style="background:rgb(149, 123, 190)">
                                    订单数：<span id="total_order">0</span>
                                </div>
                            </div>
                            <div class="form-group total-div-width" style="margin-left: 0;margin-right: 0">
                                <div class="col-sm-8 total-div" style="background: rgb(248, 215, 218)">
                                    付款预估收入：<span id="total_pre_money">0</span>
                                </div>
                            </div>
                            <div class="form-group total-div-width" style="margin-left: 0;margin-right: 0">
                                <div class="col-sm-8 total-div" style="background: rgb(190, 229, 235)">
                                    结算预估收入：<span id="total_settle_pre_money">0</span>
                                </div>
                            </div>
                            {{--                            <div class="form-group total-div-width" style="margin-left: 0;margin-right: 0">--}}
                            {{--                                <div class="col-sm-8 total-div" style="background: rgb(255, 255, 204)">--}}
                            {{--                                    点击数：<span id="total_click">0</span>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
                        </div>


                    </div>

                    <div class="box-body table-responsive no-padding"
                         style="border-bottom: 1px solid #f4f4f4;margin-bottom: 40px;margin-top: 20px;margin: 20px 10px 40px 10px;">
                        <table class="table table-hover" id="grid-table5eb91776d5c3d">
                            <thead>
                            <tr bgcolor="#e899acb8">
                                <th class="table-th" style="width: 15%">商品ID</th>
                                <th class="table-th" style="width: 5%">来源</th>
                                <th class="table-th" style="width: 40%">商品名称</th>
                                <th class="table-th" style="width: 10%">下单数量</th>
                                <th class="table-th" style="width: 20%">商品分类</th>
                                <th class="table-th" style="width: 10%">付款数量</th>

                            </tr>
                            </thead>


                            <tbody id="goods_list">


                            </tbody>


                        </table>
                    </div>

                    <div class="box-body table-responsive no-padding" style="margin: 20px 10px 40px 10px;">
                        <table class="table table-hover" id="grid-table5eb91776d5c3d" style="margin-bottom: 20px">
                            <thead>
                            <tr bgcolor="#e899acb8">
                                <th class="table-th">时间</th>
                                <th class="table-th" style="width: 40%">付款数量</th>
                                <th class="table-th">付款预估收入</th>
                                <th class="table-th">结算预估收入</th>

                            </tr>
                            </thead>


                            <tbody id="date_goods">


                            </tbody>


                        </table>
                    </div>
                    <!-- /.box-body -->


                </form>
            </div>

        </div>
    </div>

</section>
{{--<script src="/vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js"></script>--}}
{{--<script src="/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>--}}
<script type="text/javascript">
    $(function () {

        $('#start_time').datetimepicker({"format": "YYYY-MM-DD HH:mm:ss", "locale": "zh-CN","useCurrent": true});
        $('#end_time').datetimepicker({"format": "YYYY-MM-DD HH:mm:ss", "locale": "zh-CN", "useCurrent": false});
        // $("#end_time").on("dp.change", function (e) {
        //     $('#end_time').data("DateTimePicker").minDate(e.date);
        // });
        // $("#end_time").on("dp.change", function (e) {
        //     $('#start_time').data("DateTimePicker").maxDate(e.date);
        // });


        $("#cat_id_one").change(function () {
            var id = $(this).val();
            var options = '';
            $.get("/admin/goods-cat/getCat?q=" + id, function (data) {
                if (data) {
                    $('#cat_id_two').empty();
                    options += "<option value=''>请选择二级分类</option>";
                    $.each(data, function (n, value) {
                        var trs = "";
                        trs += "<option value=" + value.id + ">" + value.text + "</option>";
                        options += trs;
                    });

                    $("#cat_id_two").append(options);
                }
            })
        });

        $('#search_btn').click(function () {
            $(this).button('loading');
            var partition = $('#partition').val();
            var cat_id_one = $('#cat_id_one').val();
            var cat_id_two = $('#cat_id_two').val();
            var start_time = $('#start_time').val();
            var end_time = $('#end_time').val();
            var goods_id = $('#goods_item_id').val();
            $.ajax({
                url: "/admin/order-statics-data",
                type: 'get',
                data: {
                    partition: partition,
                    cat_id_one: cat_id_one,
                    cat_id_two: cat_id_two,
                    start_time: start_time,
                    end_time: end_time,
                    goods_id: goods_id
                },
                dataType: 'json',
                success: function (data) {
                    var options = '';
                    var day_options = '';
                    if (data) {
                        $('#goods_list').empty();
                        var goods = data.data.rank_list;
                        $.each(goods, function (n, value) {
                            var trs = "";
                            trs += "<tr><td class='table-column'>" + value.goods_item_id + "</td><td class='table-column'>" + value.source + "</td><td class='table-column'>" + value.goods_title + "</td><td class='table-column'>" + value.counts + "</td><td class='table-column'>"+value.cat_one_name+"<font color='red'>=></font>"+value.cat_two_name+"</td><td class='table-column'>" + value.had_paid + "</td></tr>";
                            options += trs;
                        });

                        $("#goods_list").append(options);
                        //top
                        $('#total_recharge').html(data.data.top_statics.total_recharge);
                        $('#total_money').html(data.data.top_statics.total_money);
                        $('#total_order').html(data.data.top_statics.total_order);
                        $('#total_pre_money').html(data.data.top_statics.total_pre_money);
                        $('#total_settle_pre_money').html(data.data.top_statics.total_settle_pre_money);
                        // $('#total_click').html(data.data.top_statics.total_click);
                        //days
                        $('#date_goods').empty();
                        var goods = data.data.day_statics;
                        $.each(goods, function (n, value) {
                            var trs = "";
                            trs += "<tr><td class='table-column'>" + value.days + "</td><td class='table-column'>" + value.counts + "</td><td class='table-column'>" + value.total_pre_money + "</td><td class='table-column'>" + value.total_settle_pre_money + "</td></tr>";
                            day_options += trs;
                        });

                        $("#date_goods").append(day_options);

                    }
                },
                //异常处理
                error: function (e) {
                    // console.log(e);
                },
                complete: function () {
                    $('#search_btn').button('reset');
                }
            })
        })
    })
</script>
</body>
</html>
