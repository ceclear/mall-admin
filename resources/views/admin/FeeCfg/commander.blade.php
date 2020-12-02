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
    </style>
</head>
<body>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $title }}</h3>

                    <div class="box-tools">
                        <div class="btn-group pull-right" style="margin-right: 5px">
                            <a href="/admin/fee-cfg" class="btn btn-sm btn-default" title="列表"><i
                                        class="fa fa-list"></i><span class="hidden-xs">&nbsp;列表</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form  id="post_form" method="post" accept-charset="UTF-8"
                      class="form-horizontal"
                      pjax-container="">

                    <div class="box-body">
                        <div class="fields-group">

                            <div class="col-md-12">
                                <div class="form-group  ">
                                    <label for="rate" class="col-sm-2  control-label">达成条件</label>
                                    <div class="col-sm-8" style="width: 80%">
                                        <div class="input-group">
                                            <select class="form-control" name="condition1"
                                                    style="float: left;width: 160px;">
                                                <option value="">请选择达成条件</option>
                                                @if(isset($select1))
                                                    @foreach($select1 as $key=>$item)
                                                        @if(!empty($current)&&$current == $key)
                                                            <option selected value="{{$key}}">{{$item}}</option>
                                                        @else
                                                            <option value="{{$key}}">{{$item}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>

                                            <select class="form-control"  name="symbol1"
                                                    style="float: left;width: 120px;margin-left: 10px">
{{--                                                <option value="">请选择范围</option>--}}
                                                @if(isset($symbol))
                                                    @foreach($symbol as $key=>$item)
                                                        @if(isset(json_decode($info['condition'])->register) == $key)
                                                            <option selected value="{{$key}}">{{$item}}</option>
                                                        @else
                                                            <option value="{{$key}}">{{$item}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>

                                            <input style="float: left;width: 60px;margin-left: 10px" type="text"
                                                   name="num1" value="{{$info['num1']}}"
                                                   class="form-control rate"
                                                   autocomplete="off" placeholder="数量">
                                            <label style="float:left;font-weight: 500; height: 30px; line-height: 37px;margin-left: 10px;">人,并且</label>

                                            <select class="form-control" name="condition2"
                                                    style="float: left;width: 160px;margin-left: 10px">
                                                <option value="">请选择达成条件</option>
                                                @if(isset($select2))
                                                    @foreach($select2 as $key=>$item)
                                                        @if(!empty($end)&&$end == $key)
                                                            <option selected value="{{$key}}">{{$item}}</option>
                                                        @else
                                                            <option value="{{$key}}">{{$item}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>

                                            <select class="form-control" name="symbol2"
                                                    style="float: left;width: 120px;margin-left: 10px">
{{--                                                <option value="">请选择范围</option>--}}
                                                @if(isset($symbol))
                                                    @foreach($symbol as $key=>$item)
                                                        @if(isset(json_decode($info['condition'])->register) == $key)
                                                            <option selected value="{{$key}}">{{$item}}33</option>
                                                        @else
                                                            <option value="{{$key}}">{{$item}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>

                                            <input style="float: left;width: 60px;margin-left: 10px" type="text"
                                                   name="num2" value="{{$info['num2']}}"
                                                   class="form-control rate"
                                                   autocomplete="off" placeholder="数量">
                                            <label style="float:left;font-weight: 500; height: 30px; line-height: 37px;margin-left: 10px;">人</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group  ">

                                    <label for="rate" class="col-sm-2  control-label">自购佣金</label>
                                    <div class="col-sm-8">
                                        <div class="input-group" style="width: 50%;float:left">

                                            {{--                                            <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>--}}
                                            <input type="text" name="self_fee_rate" value="{{$info['self_fee_rate']}}"
                                                   class="form-control rate"
                                                   autocomplete="off" placeholder="自己在平台领券购物获得的产品佣金（填数字，例 50）">
                                        </div>
                                        <div class="input-group" style="width: 50%;padding-left: 10px">
                                            <input type="text" name="subsidy_fee_rate"
                                                   value="{{$info['subsidy_fee_rate']}}"
                                                   class="form-control rate"
                                                   autocomplete="off" placeholder="自己在平台领券购物获得平台补贴（填数字，例 50）">
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group  ">
                                    <label for="rate" class="col-sm-2  control-label">团队佣金</label>
                                    <div class="col-sm-8">
                                        <div class="input-group" style="width: 50%">
                                            <input type="text" name="team_fee_one_rate"
                                                   value="{{$info['team_fee_one_rate']}}"
                                                   class="form-control rate"
                                                   autocomplete="off" placeholder="你直接推荐的（一级合伙人）领券购物获得的佣金（填数字，例 50）">
                                        </div>
                                        <div class="input-group" style="margin-top: 10px;width: 50%">
                                            <input type="text" name="team_fee_two_rate"
                                                   value="{{$info['team_fee_two_rate']}}"
                                                   class="form-control rate"
                                                   autocomplete="off"
                                                   placeholder="你的一级合伙人直接推荐的（二级合伙人）领券购物获得的佣金（填数字，例 50）">
                                        </div>
                                        <div class="input-group" style="margin-top: 10px;width: 50%">
                                            <input type="text" name="team_fee_infinite_rate"
                                                   value="{{$info['team_fee_infinite_rate']}}"
                                                   class="form-control rate"
                                                   autocomplete="off"
                                                   placeholder="你二级以外的合伙人（无限级）领券购物获得的佣金（填数字，例 50）">
                                        </div>
                                        <div class="input-group" style="margin-top: 10px;width: 50%">
                                            <input type="text" name="colonel_one_rate"
                                                   value="{{$info['rate_arr']['rates']['colonel_one_rate']}}"
                                                   class="form-control rate"
                                                   autocomplete="off"
                                                   placeholder="你的一级有人成为团长,平台额外给你他团队收益（填数字，例 50）">
                                        </div>
                                        <div class="input-group" style="margin-top: 10px;width: 50%">
                                            <input type="text" name="colonel_two_rate"
                                                   value="{{$info['rate_arr']['rates']['colonel_two_rate']}}"
                                                   class="form-control rate"
                                                   autocomplete="off"
                                                   placeholder="你的二级有人成为团长,平台额外给你他团队收益（填数字，例 50）">
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group  ">
                                    <label for="rate" class="col-sm-2  control-label">分享佣金</label>
                                    <div class="col-sm-8">
                                        <div class="input-group" style="width: 50%">
                                            <input type="text" name="share_fee_rate" value="{{$info['share_fee_rate']}}"
                                                   class="form-control rate"
                                                   autocomplete="off" placeholder="邀请好友领券购物获得的佣金（填数字，例 50）">
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group  " id="sw-content">
                                    <label for="_status" class="col-sm-2  control-label">状态</label>
                                    <div class="col-sm-8">
                                        <input type="checkbox" name="_status" class="make-switch" checked
                                               data-on-color="default" data-off-color="primary">

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">

                        <div class="col-md-2">
                        </div>
                        <input type="hidden" name="id" value="{{$info['id']}}">
                        <input type="hidden" name="type" value="{{$info['type']??2}}">
                        <input type="hidden" id="status" name="status" value="1">
                        <div class="col-md-8">

                            <div class="btn-group pull-right">
                                <button id="sub" type="button" data-loading-text="Loading..." class="btn btn-primary">
                                    提交
                                </button>
                            </div>


                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning">重置</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>

</section>
<script type="text/javascript">
    $(document).ready(function () {
        //基本初始化
        // $('#sw-content input').bootstrapSwitch();
        var initStatus = "{{$info['status']}}";
        var initShow = initStatus == 1 ? true : false;
        console.log(initStatus, initShow);
        //手动设置按钮状态
        $('#sw-content input').bootstrapSwitch('state', initShow);

        //点击按钮切换 switch
        // $('#oper-btn-sw').click(function () {
        //     $('#sw-content input').bootstrapSwitch('toggleState');
        // });

        //点击触发事件，监听按钮状态
        $('#sw-content input').on('switchChange.bootstrapSwitch', function (event, _status) {
            //内置对象、内置属性
            // console.log(event);
            var st = _status == true ? 1 : 0;
            $('#status').val(st)
            //获取状态
            // console.log(_status);
        });
    })
    $(function () {
        $('#sub').click(function () {
            $(this).button('loading');
            var data = $("#post_form").serialize();
            $.ajax({
                url: "/admin/fee-cfg/commander-submit",
                type: 'post',
                data: data,
                dataType: 'json',
                timeout: 10000,
                success: function (data) {
                    swal(data.msg)
                },
                //异常处理
                error: function (e) {
                    // console.log(e);
                },
                complete: function () {
                    $('#sub').button('reset');
                }
            })
        })
    })
</script>
</body>
</html>
