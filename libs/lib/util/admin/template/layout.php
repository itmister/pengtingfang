<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
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
    <!--
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">

    -->

    <link href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.css" rel="stylesheet">
    <link href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap-theme.min.css" rel="stylesheet">
    <!--<link href="http://apps.bdimg.com/libs/fontawesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">-->
    <link href="//cdn.bootcss.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">
    <style rel="stylesheet">
        <?=file_get_contents( __DIR__ . '/css/main.css'); ?>
    </style>

    <!--<script src="//cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>-->
    <!--<script src="//cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>-->
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
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
        <?=file_get_contents( __DIR__  . '/js/main.js'); ?>
    </script>
    <!--<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>-->
    <script src="http://apps.bdimg.com/libs/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <!--//cdn.bootcss.com/font-awesome/3.2.1/font/fontawesome-webfont.svg-->
</head>
<body>
    <? \View::i()->fetch('nav', '.php', dirname(__FILE__) . '/'); ?>
    <div class="container" id="main_container">
        <div class="row">
                <!--Sidebar content-->
                <div class="panel-group hide-sm nav-sitemenu col-md-2" id="col_left">
                    <? \View::i()->fetch('menu', '.php', dirname(__FILE__) . '/'); ?>
                </div>
                <div class="col-md-10" id="col_main">
                    <?= $breadcrumb; ?>
                    <? if (!empty($tpl_content)) \View::i()->fetch($tpl_content) ?>
                </div>
        </div>
</body>