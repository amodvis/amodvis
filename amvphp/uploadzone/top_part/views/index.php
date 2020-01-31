<div class="module_top_part">
    <!--md_topline end-->
    <div class="md_nav clearfix">
        <div class="md_navbox fl">
            <ul class="J_FileCate">
                <li class="on" data-id="0">全部图片(<em><?php echo $all_count; ?></em>)</li>
                <?php
                foreach ($category_list as $item) {
                    echo ' <li data-id="' . $item->id . '">' . $item->path_name . '</li>';
                }
                ?>
            </ul>
        </div>
        <div class="md_topline fr">
            <div class="J_UploadZoneUpload upload_zone_upload">
                <div class="upload_text">上传文件</div>
            </div>
        </div>
        <div class="md_makenav fr">
            <button type="button"
                    class="md_addnavopen layui-md-btn layui-btn-normal layui-btn-long100">新建分组
            </button>
            <div class="md_addnavbox">
                <div class="arrow-top arrow-box">
                    <b class="top"><i class="top-arrow1"></i><i class="top-arrow2"></i></b>
                </div>
                <div class="md_tit">请输入分组名称</div>
                <div class="md_ipt"><input type="text" placeholder="请输入分组名称" class="J_AddedCateName"></div>
                <div class="md_subbtn">
                    <button type="button" class="layui-md-btn J_SubbmitBtn">确定
                    </button>
                    <button type="button" class="layui-md-btn layui-btn-normal J_CancelBtn"
                            style="margin-left:15px;">取消
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>