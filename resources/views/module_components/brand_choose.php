<div class="manual-rec-content clearfix item_choose sys_company_brand_search">
    <div class="items-count J_itemCondition">
        <a href="javascript:void(0);" class="all selected J_TGetAllItems">全部(<span class="J_TTotalCount"></span>)</a>
        <a href="javascript:void(0);" class="chosed J_TGetHasChosed">已选择(<span class="J_TChosedCount"></span>)</a>
    </div>

    <div class="item-srch">
        <div class="srch-conds clearfix">
            <div class="control-group srch-cond search_type form" autocomplete='off' >
                <label class="lab">
                    <select id="J_ComBrandSearchType" class="fl">
                        <option value="2">品牌ID</option>
                    </select>
                </label>
                <div class="info">
                    <input type="text" value=""  id="searchTxt" class="txt fl txt2 txtfb J_QueryVal" val="">
                </div>
            </div>
            <!--            <div class="control-group srch-cond price">-->
            <!--                <label for="" class="control-label">价格</label>-->
            <!--                <div class="control">-->
            <!--                    <input type="text" name="lowPrice" class="input-box low-price-input J_TPriceInput">-->
            <!--                    <span>-</span>-->
            <!--                    <input type="text" name="highPrice" class="input-box high-price-input J_TPriceInput">-->
            <!--                </div>-->
            <!--            </div>-->
            <div class="control-group srch-action">
                <span class="srch-btn J_TSrchBtn"></span>
            </div>


        </div>
    </div>
    <div class="p30">
    <!-- 搜索出来的宝贝列表 -->
    <table class="tb_m w tc mb20">
        <thead>
        <tr class="bg">
            <th class="tc">选择</th>
            <th   style="width:160px;" class="tc">ID</th>
            <th   style="width:160px;" class="tc">主图</th>
            <th   style="width:260px;" class="tc">详细</th>
            <th style="" class="tc">操作项</th>
        </tr>
        </thead>
        <tbody  class="J_Content">

        </tbody>
    </table>
    </div>
    <div id="item_query_loadingPins" class="loadingPins"></div>

    <!-- 翻页 -->
    <div id="brand_query_J_PagesBottom" class="edit-pagination">

    </div>
</div>
<script type="tpl" id="tpl" class="J_Brand_list_tpl">
    <tr class="item"  data-id="{id}"  data-item-id="{item_id}">
        <td class="checkbox_item"><span class="select_input"><label><input type="checkbox" value="{id}" name="p_list_id"></label></span></td>
        <td class="detail_info">{id}</td>
        <td class="pic"><a target="_blank" href="{detailsUrl}"><img src="{logo}" alt="宝贝图片"></a></td>
        <td class="detail_info">
            {name}
        </td>
        <td class="opts">
            <a class="" href="/modulepage/super/hot?debug=1&brand_id={id}&params=%7B%22module_tag%22:{id}%7D" target="_blank">修改（hot）页面</a>
            <div class="pt10"></div>
            <a class="" href="/modulepage/super/brandpresale?debug=1&brand_id={id}&params=%7B%22module_tag%22:{id}%7D" target="_blank">修改（预告）页面</a>
        </td>
    </tr>
    <tr><td colspan="5"><div style="height:1px;border-bottom:1px solid #d6d6d6;padding:5px;"></div></td></tr>
</script>
