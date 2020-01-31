<div class="admin_nav">
    <div class="logo fl"><a href="/create_app">Amodvis</a></div>
    <div class="app_name bt_bd_nav fr">
        <div class="menu_trigger">切换应用</div>
        <div class="app_pop clearfix">
            <a href="/create_app"><span>应用管理</span></a>
            <?php
            foreach ($app_list_ret['data'] as $row) {
                echo '<a href="/pages_info/' . $row->app_name . '">' . $row->app_name_cn . '</a>';
            }
            ?>
        </div>
    </div>
    <div class="link_nav bt_bd_nav fr">
        <a target="_blank" href="/media_library">素材库</a>
    </div>
</div>
