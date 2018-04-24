<script>
    $(function(){
        _crud.init();
    })
</script>
<form class="form" method="post" id="frm_content" action="<?=$urls['index']; ?>">
    <input type="hidden" value="1" name="submit" />
    <input type="hidden" name="p" value="<?=$_REQUEST['p']; ?>"/>
    <div class="panel panel-primary">
        <div class="panel-body">
                    <div class="input-group">
                        <button type="button" class="btn btn-primary btn_crud_add"
                            href="<?=$urls['add']; ?>">新增</button>
                    </div>
        </div>
    </div>
</form>
<div id="panel_crud_add" class="hidden"></div><!--新增-->
<div id="panel_crud_edit" class="hidden"></div><!--编辑-->
<div class="content-toolbar btn-toolbar clearfix">
    <div data-toggle="buttons" class="btn-group layout-btns">
        <label class="btn btn-default btn-sm layout-normal">
            <input type="radio"> <i class="icon-th-large"></i>
        </label>
        <label class="btn btn-default btn-sm layout-condensed">
            <input type="radio"> <i class="icon-th"></i>
        </label>

    </div>
    <div data-toggle="buttons-checkbox" class="btn-group layout-btns">
        <button class="btn btn-success btn-sm layout-full" type="button"><i class="icon-fullscreen"></i></button>
    </div>

    <div data-toggle="buttons-checkbox" class="btn-group layout-btns">
        <button class="btn btn-danger btn-sm batch_delete" type="button"><i class="icon-remove"></i>批量删除</button>
    </div>
</div>
<div class="panel panel-info" id="panel_crud_data">
    <div class="table-responsive">
        <?=$html_data_table ?>
    </div>
</div>
<?=$page; ?>