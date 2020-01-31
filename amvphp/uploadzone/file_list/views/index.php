<div class="module_file_list">
    <div class="md_sclist clearfix">
        <?php
        $inner_page_from = request()->input('inner_page_from');
        $cursor_class = '';
        if ('upload_zone_home' === $inner_page_from) {
            $cursor_class = 'no_pointor';
        }
        foreach ($items['data'] as $file_item) {
            ?>
            <div class="md_oneitem <?php echo $cursor_class; ?>" data-id="<?php echo $file_item->id; ?>"
                 data-media="<?php echo $file_item->file_name; ?>">
                <div class="itembox">
                    <div class="item-img">
                        <i></i><?php if (1 == $file_item->file_type) { ?><img class="imglazyload"
                                                                              src="<?php echo $file_item->file_name; ?>"
                                                                              data-src="<?php echo $file_item->file_name; ?>"
                            ><?php } elseif (2 == $file_item->file_type) {
                            echo '<span>视频</span>';
                        } elseif (3 == $file_item->file_type) {
                            echo '<span>音频</span>';
                        } elseif (4 == $file_item->file_type) {
                            echo '<span>其他类型文件</span>';
                        } ?>
                    </div>
                    <div class="infobox">
                        <div class="item-info">
                            <div class="title">
                                <input class="J_ItemTitleInput" type="text" value="<?php echo $file_item->nick_name; ?>"
                                       autocomplete="off"/>
                            </div>
                        </div>
                        <div class="metas">
                            <a href="javascript:;"
                               class="del J_ItemDel">删除</a>
                            <div class="catelist">
                                <span class="cate ellips"><?php echo $file_item->path_name; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="J_PagesBottom page_items"></div>
    <div class="J_ItemTemplate hidden">
        <div class="md_oneitem  <?php echo $cursor_class; ?>" data-id="{id}" data-media="{file_name}">
            <div class="itembox">
                <div class="item-img">
                    <i></i>{file_html_tag}
                </div>
                <div class="infobox">
                    <div class="item-info">
                        <div class="title">
                            <input class="J_ItemTitleInput" type="text" value="{nick_name}" autocomplete="off">
                        </div>
                    </div>
                    <div class="metas">
                        <a href="javascript:;"
                           class="del J_ItemDel">删除</a>
                        <div class="catelist">
                            <span class="cate ellips">{path_name}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        webConfig.uploadZoneItemCount =<?php echo $items['total'];?>;
        <?php
        if ($kissy_use) {
            echo $kissy_use;
        }
        ?>
    </script>
</div>
