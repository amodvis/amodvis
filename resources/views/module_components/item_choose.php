<div class="manual-rec-content clearfix item_choose sys_item_search">
    <div class="items-count J_itemCondition">
        <a href="javascript:void(0);" class="all selected J_TGetAllItems">全部(<span class="J_TTotalCount">0</span>)</a>
        <a href="javascript:void(0);" class="choose J_TGetHasChoose">已选择(<span class="J_TchooseCount">0</span>)</a>
    </div>
    <div class="item-srch">
        <div class="srch-conds clearfix">
            <div class="control-group srch-cond search_type form" autocomplete='off'>
                <label class="fl hidden">
                    <select class="fl pop_item_select J_SelectOne">
                        <option value="0">加载中</option>
                    </select>
                </label>
                <label class="fl hidden">
                    <select class="fl pop_item_select J_SelectTwo">
                        <option value="0">请选择系统店铺</option>
                    </select>
                </label>
                <label class="fl">
                    <select id="searchType" class="fl pop_item_select">
                        <option value="0">按类型查找</option>
                        <option value="1">商品ID</option>
                        <option value="2">店铺ID</option>
                        <option value="3">门店ID</option>
                    </select>
                </label>
                <div class="info">
                    <input type="text" value="" id="searchTxt" class="txt fl txt2 txtfb" val="">
                </div>
            </div>
            <div class="control-group srch-action">
                <span class="srch-btn J_TSrchBtn" style="margin-top: 4px;"></span>
            </div>


        </div>
    </div>
    <div class="p30">
        <!-- 搜索出来的宝贝列表 -->
        <table class="tb_m w tc mb20">
            <thead>
            <tr class="bg">
                <th class="J_MoveBox tc" style="display: none;">移动</th>
                <th style="width:80px;" class="tc">主图</th>
                <th style="width:360px;" class="tc">详细</th>
                <th style="" class="tc">操作项</th>
            </tr>
            </thead>
            <tbody class="J_Content">

            </tbody>
        </table>
    </div>
    <div id="item_query_loadingPins" class="loadingPins"></div>


    <!-- 翻页 -->
    <div id="item_query_J_PagesBottom" class="edit-pagination">

    </div>
</div>

<script type="tpl" id="tpl" class="J_Item_tpl">

 <tr class="amod_item"  data-id="{id}"  data-item-id="{item_id}" height=80>
        <td  class="J_MoveBox" style="display:{moveBoxShow}"><div class="order_move J_OrderMove"><b class="move_top" alt="置顶" title="置顶"></b><b class="move_btm" alt="置尾" title="置尾"></b><b class="move_up"  alt="去上一位" title="去上一位"></b><b class="move_down" alt="去下一位" title="去下一位"></b></div></td>
        <td class="pic"><a target="_blank" href="{detailsUrl}"><img src="{img}" alt="宝贝图片"></a></td>
        <td class="detail_info">
            <p class="title"><a target="_blank" href="{detailsUrl}">{title}</a></p>
            <div class="price_wrap">
            {price_html}
            </div>
         </td>
          <td class="options" height=80 width=80>
                   <a class="rec-opt J_SettingOpt" style="display:{none_setting_tag};margin-bottom: 10px;" href="javascript:void(0);">设置</a>
                   <a class="rec-opt J_TRecOpt" href="javascript:void(0);">{select_tag}</a>
          </td>
    </tr>


</script>
