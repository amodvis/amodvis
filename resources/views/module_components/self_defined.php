<div class="control-group admin_shop_self_defined">
    <form method="POST" name="moduleSetForm" class="auto-rec-form form-default" action="<?php echo $action; ?>">
        <div class="setting setting-mod-custom">
            <textarea class="self_content" name="self_content" style="width:780px; height:270px;"></textarea>
        </div>
        <div class="opt-footer">
            <input type="button" value="保存" class="btn-ok J_Btn-ok">
        </div>
    </form>
    <script>
        $(function () {
            $('.self_content').val($('textarea[name="' + webConfig.trigger + '"]').val());
            window.editor = KindEditor.create('.self_content', {
                filterMode: false,
                cssPath: PUBLIC_URL + 'js/kindeditor-4.1.10/plugins/code/prettify.css',
                uploadJson: UPLOAD_URL + 'moduleapi/upload/uploadeditor?type=flash',
                allowFileManager: true,
                afterCreate: function () {
                    var self = this;
                    KindEditor.ctrl(document, 13, function () {
                        self.sync();
                        K('form[name=example]')[0].submit();
                    });
                    KindEditor.ctrl(self.edit.doc, 13, function () {
                        self.sync();
                        KindEditor('form[name=example]')[0].submit();
                    });
                },
                items: [
                    'source', '|', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                    'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                    'insertunorderedlist', '|', 'emoticons', 'link']
            });
        });
    </script>
</div>
