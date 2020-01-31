<div id="mods-list" class="module_choose">
    <div class="mods_list">
        <?php
        $contentClass = 'my_stage';
        $navClass = 'my_trigger';
        $pannelClass = 'stage_item';
        $selectedClass = 'cur';
        $prevBtnCls = 'tu_prev';
        $nextBtnCls = 'tu_next';
        ?>
        <div data-widget-config="{
                         'eventType':'click',
                         'effect':'none',
                         'autoSlide':false,
                         'easing':'easeBoth',
                         'navClass':'<?php echo $navClass; ?>',
                         'contentClass':'<?php echo $contentClass; ?>',
                         'pannelClass':'<?php echo $pannelClass; ?>',
                         'selectedClass':'<?php echo $selectedClass; ?>',
                         'prevBtnCls':'<?php echo $prevBtnCls; ?>',
                          'nextBtnCls':'<?php echo $nextBtnCls; ?>'
                         }" class="J_TWidget slide_box">
            <ul class="<?php echo $navClass; ?>">
                <li data-id="mall_infinity">mall_infinity</li>
                <li data-id="home_company">home_company</li>
                <li data-id="public_project">系统公共模块</li>
            </ul>
            <ul class="<?php echo $contentClass; ?>">
                <li class="<?php echo $pannelClass; ?>"><div class="mods-num">共有<span class="highlight">17</span>模块</div><div class="loadingPins"></div><div class="ColumnContainer"></div><div class="edit-pagination J_PagesBottom J_PagesBottom"></div></li>
                <li class="<?php echo $pannelClass; ?>"><div class="mods-num">共有<span class="highlight">17</span>模块</div><div class="loadingPins"></div><div class="ColumnContainer"></div><div class="edit-pagination J_PagesBottom J_PagesBottom"></div></li>
                <li class="<?php echo $pannelClass; ?>"><div class="mods-num">共有<span class="highlight">17</span>模块</div><div class="loadingPins"></div><div class="ColumnContainer"></div><div class="edit-pagination J_PagesBottom"></div></li>
            </ul>
        </div>


        <div class="hide J_Item_tpl">
            <div class="pin_item">
                <div class="thumb">
                    <img src="{thumbnail}">
                </div>
                <p>
                    <strong>{nick_name}</strong>
                    {des}<br>
                </p>
                <div class="fl">
                    <a style="height: 18px;line-height: 18px;width: auto;" class="btn btn-ok" data_module_name="{module_name}"
                       data_nick_name="{nick_name}"
                       data_project_name="{project_name}">
                        添加
                    </a>
                </div>
           </div>
        </div>
        <script>
            KISSY.use("modules/module_init/init_widget_base", function (S, loadWidget) {
                loadWidget.functions.loadWidget(".J_TWidget");
            });
        </script>
    </div>
</div>
