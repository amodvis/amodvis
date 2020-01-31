<div class="control-group admin_shop_self_defined">
    <form method="POST" name="moduleSetForm" class="auto-rec-form form-default" action="<?php echo $action;?>"
          autocomplete="off">
        <label for="" class="control-label">显示标题：</label>

        <div class="control show-title">
            <?php
            if (isset($data['showTitle'])&&$data['showTitle']==="true") {
                $checkString1 = "";
                $checkString2 = 'checked="checked"';
                $title_class = 'display:inline;';
            } else {
                $checkString1 = 'checked="checked"';
                $checkString2 = '';
                $title_class = 'display:none;';
            }
            ?>
            <label>
                <input type="radio" value="false" name="showTitle" class="J_TNotShowTitle  input-radio" <?php echo $checkString1;?>>不显示
            </label>
            <label>
                <input  type="radio" <?php echo $checkString2;?> value="true" name="showTitle" class="J_TShowTitle  show-title-true input-radio">显示
            </label>
            <input type="text" value="<?php  if(isset($data['show_title_val'])){ echo $data['show_title_val'];}?>" name="show_title_val" maxlength="30" class="J_TTitleInput input-box title-input" style="<?php echo $title_class;?>">
        </div>
        <div class="setting setting-mod-custom">
            <textarea class="self_content" name="self_content" style="width:780px; height:270px;"></textarea>
        </div>
        <div class="opt-footer">
            <input type="button" value="保存" class="btn-ok J_Btn-ok">
        </div>
    </form>
    <script>
        $(function(){
            KindEditor.ready(function(K) {
                window.editor =  K.create('.self_content', {
                    filterMode: false,
                    cssPath : PUBLIC_URL+'/plugins/code/prettify.css',
                    uploadJson : UPLOAD_URL+'moduleapi/upload/uploadeditor?type=flash',
                    allowFileManager : true,
                    afterCreate : function() {
                        var self = this;
                        K.ctrl(document, 13, function() {
                            self.sync();
                            K('form[name=example]')[0].submit();
                        });
                        K.ctrl(self.edit.doc, 13, function() {
                            self.sync();
                            K('form[name=example]')[0].submit();
                        });
                    },
                    items : [
                        'source','|','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                        'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                        'insertunorderedlist', '|', 'emoticons','link']
                });

                <?php
                if(isset($data['self_content'])){
                ?>
                var htmlDetail = '<?php echo preg_replace('/\n||\r/', "", addslashes($data['self_content'])); ?>';
                editor.html(htmlDetail);
                <?php
                }
                ?>

            });


        });
    </script>
</div>
