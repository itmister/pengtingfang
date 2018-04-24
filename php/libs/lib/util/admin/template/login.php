<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?=$title; ?></title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <style>
        body {
            color: #000;
            font-size: 12px;
            font-family: "Helvetica Neue",Helvetica,STheiti,微软雅黑,宋体,Arial,Tahoma,sans-serif,serif;
        }
        #main_container {
            padding-top : 60px;
            width: 100%;
        }
    </style>
    <!-- Bootstrap -->
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">

    <!--<script src="//cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>-->
    <script src="http://libs.baidu.com/jquery/2.1.1/jquery.min.js"></script>
    <script src="http://apps.bdimg.com/libs/layer/2.0/layer.js"></script>

    <script>
        //兼容插件对jquery移除的特性$.browser的依赖
        if ( typeof($.browser) == 'undefined') {
            $.browser = {};
            $.browser.mozilla = /firefox/.test(navigator.userAgent.toLowerCase());
            $.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
            $.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
            $.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
        }
        <?=file_get_contents( __DIR__ . '/js/main.js'); ?>

    </script>
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script>
        $(function(){
            $('#frm_login').submit(function(){
                var $this = $(this);
                var param = $this.serialize();
                _lib.post($this.attr('action'), param, function(d){
                    window.location = d.data.url;
                });
                return false;
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <form class="form" id="frm_login" action="<?=$url_login; ?>">
                    <div class="panel panel-primary" style="margin-top:50px;">
                        <div class="panel-heading">
                            <h1 >
                                <i class="icon-coffee"></i><?=$title; ?>
                            </h1>
                        </div>
                        <div class="panel-body">
                                <input type="hidden" name="submit" value="1"/>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-user"></i></span>
                                        <input class="form-control input-lg" type="text" placeholder="帐号" name="user_name" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-lock"></i></span>
                                        <input class="form-control input-lg" type="password" placeholder="密码" name="password" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-success btn-lg pull-right" type="submit"><i class="icon-ok"></i>&nbsp登录</button>
                                </div>
                        </div>
                        <div class="panel-footer text-right">
                            <span><?=\Util\Net\Ip::get_instance()->get_client_ip(); ?></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>