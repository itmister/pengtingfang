<? if (is_array($admin_menu) ) foreach ($admin_menu as $menu ): ?>
<div class="panel panel-info fixed">
    <div class="panel-heading">
        <h6 class="panel-title">
            <span class="badge badge-info"></span>
            <? if ( !empty($menu['children'])): ?>
                <a href="#nav-panel-0" data-parent="#nav-accordion" data-toggle="collapse" class="accordion-toggle">
                    <i class="icon-fixed-width icon-briefcase"></i>
                    <?= $menu['title']; ?>
                </a>
            <? else: ?>
                <a href="<?=$menu['url']; ?>" >
                    <i class="icon-fixed-width icon-briefcase"></i>
                    <?= $menu['title']; ?>
                </a>
            <? endif; ?>
        </h6>
    </div>
    <? if (!empty($menu['children']) ) foreach ( $menu['children'] as $action ): ?>
        <div class="list-group panel-collapse collapse in" id="nav-panel-0">
            <a class="list-group-item <?= !empty($action['active']) ? 'active' : '' ?>"  href="<?=$action['url'] ?>">
                <i class="icon-fixed-width icon-globe"></i>
                <?= $action['title']; ?>
            </a>
        </div>
    <? endforeach; ?>
</div>
<? endforeach; ?>