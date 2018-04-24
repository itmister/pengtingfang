/**
 * Created by vl on 2015/10/20.
 */
var _lib = {
    msg : {
        success : function(msg) {
            layer.alert(msg, 1)
        },
        error : function(msg) {
            layer.alert(msg, 3)
        },
        tip: function(msg) {
            layer.msg(msg);
        }
    },

    confirm : function(msg, ok_callback ) {
        var w_idx = layer.confirm(msg,function(){
            layer.close(w_idx);
            if (ok_callback) ok_callback();
        });
    },

    question : function() {
    },

    form_init: function( $frm ) {
        $frm.find('input[type=input]').val("");
        $frm.find('textarea').val("");
    },

    post : function (url,params,callback) {
        url = url + '&r' + Math.random();
        var layer_loading = layer.load('正在提交')
        $.post(url,params, function(d) {
            layer.close(layer_loading);
            if ( typeof(d) == 'undefined' || typeof(d.code) == 'undefined' ) {
                _lib.msg.error("未知错误");
            }

            if ( d.code != 0 ) {
                var msg = typeof(d.msg) != 'undefined' ? d.msg : ( typeof (d.message) != 'undefined' ? d.message : '未知错误');
                _lib.msg.error( msg );
                return false;
            }
            if (callback) callback(d);
        })
    },

    /**
     *
     * @param $btn jquery按钮对象
     * @param pattern 扩展名匹配正则 /(jpg)|(jpeg)/i
     * @param callback_success 上传成功后回调
     */
    ajax_upload : function($btn, pattern, callback_success) {
        var upload_option = {
            action : $btn.attr('href'),//提交目标
            name : $btn.attr('name'),// 服务端接收的名称
            autoSubmit : true,// 自动提交
            responseType: 'json',
            onChange : function(file, extension) {// 选择文件之后…
                if ( pattern && !new RegExp(pattern).test(extension) ) {
                    _lib.msg.error("只限上传" + pattern.toString() + "格式文件，请重新选择！");
                    return false;
                }
                $btn.attr('org_val', $btn.val());
                $btn.val("正在上传…");
                $btn.attr("disabled",true);
                return true;
            },
            onSubmit : function(file, extension) { // 开始上传文件
                //					$("#state").val("正在上传" + file + "..");
                //layer.load('正上传');
            },
            onComplete : function(file, response) {// 上传完成之后
                //layer.close();
                $btn.removeAttr("disabled");
                $btn.val( $btn.attr('org_val') );
                if ( !response.success ) {
                    _lib.msg.error( response.msg );
                    return false;
                }
                if ( callback_success ) callback_success( response );
            }
        }
        // 初始化图片上传框
        var oAjaxUpload = new AjaxUpload( $btn, upload_option  );
    }
}

var _crud = {
    $_edit_row : null,
    $panel_crud_add : null,
    $panel_crud_edit : null,
    $panel_crud_data : null,//数据内容面板
    $frm_content : null,//内容筛选表单

    _callback_add_render : null,//新增面板渲染回调
    _callback_edit_render : null,//编辑面板渲染回调
    _callback_list_render : null,//数据表渲染回调

    init : function(opt) {
        if (typeof(opt) != 'undefined') {
            if ( opt.callback_add_render ) _crud._callback_add_render = opt.callback_add_render;
            if ( opt.callback_edit_render ) _crud._callback_edit_render = opt.callback_edit_render;
            if ( opt.callback_list_render ) _crud._callback_list_render = opt.callback_list_render;
        }
        _crud.$panel_crud_add        = $('#panel_crud_add');
        _crud.$panel_crud_edit       = $('#panel_crud_edit');
        _crud.$frm_content           = $('#frm_content');
        _crud.$panel_crud_data       = $('#panel_crud_data');
        _crud.$panel_crud_edit.css('position', 'absolute');
        $('.btn_crud_add').click(function(){
            _crud.add_show( $(this).attr('href') );
            return false;
        });
        this.data_init();
    },

    data_init : function( html_data_table ) {
        var $panel_crud_data = _crud.$panel_crud_data;
        _crud.edit_close();
        if ( typeof (html_data_table) != 'undefined' ) _crud.$panel_crud_data.html( html_data_table );

        $('.chk_all').click(function(){
            is_checked = $(this).is(':checked');
            if (is_checked)
                _crud.$panel_crud_data.find('input[type=checkbox]').prop('checked', true);
            else
                _crud.$panel_crud_data.find('input[type=checkbox]').removeAttr('checked');
        });

        //编辑行
        _crud.$panel_crud_data.find('.btn_crud_edit').click(function(){
            var data_id = $(this).attr('data_id');
            _crud.edit_show( $('#row_' + data_id), $(this).attr('href') );
        });

        _crud.$panel_crud_data.find('.btn_crud_delete').click(function(){
            var $this = $(this);
            var url = $this.attr('href');
            _lib.confirm("确定删除?",function(){
               var params =  _crud.$frm_content.serializeArray();
                var data_id = $this.attr('data_id');
                if (typeof(data_id) != 'undefined') params.push({name : 'id', value : data_id});
               _lib.post( url , params, function(d){
                   if (d.data.html_data_table) _crud.data_init( d.data.html_data_table );
               })
           });
        });

        if ( _crud._callback_list_render ) _crud._callback_list_render( $panel_crud_data );
    },

    add_show : function( url ) {
        var params =  _crud.$frm_content.serializeArray();
        for (var i in params ) if ( params[i]['name'] == 'submit' ) params[i]['value'] = 0;
        _lib.post( url, params, function( d ) {
            _crud._add_box_render(d);
        });
    },

    _add_box_render : function(d) {
        $panel_crud_add = _crud.$panel_crud_add;
        $panel_crud_add.html(d.data.content);
        $panel_crud_add.removeClass('hidden');
        $panel_crud_add.find("input[type=input]:first").focus();


        $panel_crud_add.find('form').submit(function(){
            var $this = $(this);
            _lib.post( $this.attr('action'), $this.serialize(), function(d){
                _lib.msg.tip('新增成功');
                _crud._add_box_render( d );//新增后表单刷新
            });
            return false;
        });

        $panel_crud_add.find('.btn_crud_add_cancel').click(function(){
            _crud.add_close();
        })

        if (d.data.html_data_table) _crud.data_init(d.data.html_data_table);

        //回调
        if (_crud._callback_add_render) _crud._callback_add_render( $panel_crud_add );
    },

    add_close : function() {
        $panel_crud_add = _crud.$panel_crud_add;
        $panel_crud_add.addClass('hidden')
    },

    edit_show : function( $_edit_row, url ) {
        _crud.edit_close();
        _crud.$_edit_row = $_edit_row;
        var data_id = $_edit_row.attr('data_id');
        var params =  _crud.$frm_content.serializeArray();
        for (var i in params ) if ( params[i]['name'] == 'submit' ) params[i]['value'] = 0;
        if (typeof( data_id) != 'undefined') params.push( {name : 'id', value:data_id} );
        _lib.post(url, params, function(d){
            $panel_crud_edit = _crud.$panel_crud_edit;
            $panel_crud_edit.removeClass('hidden');
            $panel_crud_edit.html(d.data.content);
            $panel_crud_edit.find("input[name=data_id]").val(data_id);
            var frm_height = $panel_crud_edit.height();
            if ( !$_edit_row.attr('height_org') ) $_edit_row.attr('height_org', $_edit_row.height() );
            $_edit_row.height(frm_height);
            var row_width = $_edit_row.width();

            $panel_crud_edit.width( row_width );
            var row_offset = $_edit_row.position();
            $panel_crud_edit.css( row_offset );
            $panel_crud_edit.find('.btn_crud_edit_cancel').click(function(){
                _crud.edit_close();
            });

            $panel_crud_edit.find('form').submit(function(d){
                $this = $(this);
                _lib.post( $this.attr('action'), $this.serialize(), function(d){
                    _crud.edit_close();
                    if (d.data.html_data_table) _crud.data_init(d.data.html_data_table);
                    _lib.msg.tip('编辑成功');
                });
               return false;
            });

            if (_crud._callback_edit_render ) _crud._callback_edit_render( $panel_crud_edit );
        });


    },

    edit_close : function() {
        _crud.$panel_crud_edit.addClass('hidden');
        if ( _crud.$_edit_row ) _crud.$_edit_row.height( _crud.$_edit_row.attr('height_org') );
    }
}


$.fn.extend({
    ajax_select : function() {
        $(this).on('change', function(){
            $this = $(this);
            var id = $this.val();
            _lib.post($this.attr('url'), {id:id}, function(d){
                var s = [];
                if (d.data ) for (var i in d.data ) s.push('<option value="' + d.data[i].id + '">' + d.data[i].title + '</option>');
                $( $this.attr('data_target')).html( s.join("\n") );
            });
        });
    }
});

$(function(){
    $('.layout-full').click(function(){
        if ( $(this).hasClass('active')) {
            $('#col_left').css('display', 'block');
            $('#col_main').attr('class', 'col-md-10');
            $('#col_main').find('.breadcrumb').css('display', 'block');
        }
        else {
            $('#col_left').css('display', 'none');
            $('#col_main').attr('class', 'col-md-12');
            $('#col_main').find('.breadcrumb').css('display', 'none');
        }
    });
    $('.ajax_select').ajax_select();
});