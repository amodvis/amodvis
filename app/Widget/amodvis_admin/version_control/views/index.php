<?php if (App::environment(DEV)) { ?>
    <style>
        .version_control_module .version_now {
            width: 322px;
            height: 30px;
            background: #0480ff;
            color: #ffffff;
            text-align: center;
            line-height: 30px;
            position: fixed;
            right: 0;
            bottom: 0;
            font-size: 12px;
            z-index: 99999;
        }

        .version_control_module .version_now_name {
            width: 140px;
            border-left: 1px solid rgb(2, 109, 217);
            float: left;
        }

        .version_control_module .change_version {
            border-left: 1px solid rgb(2, 109, 217);
            cursor: pointer;
            width: 140px;
            float: left;
        }

        .version_control_module .change_version a {
            color: #ffffff;
        }

        .version_control_module .close_open_set {
            width: 40px;
            float: left;
            cursor: pointer;
        }

        .version_control_module .close .version_now_name {
            display: none;
        }

        .version_control_module .close {
            width: 40px;
        }

        .version_control_module .close .change_version {
            display: none;
        }
    </style>
<?php } ?>
<?php

use App\Classes\Utils\UtilsCommon;

?>
<?php if (App::environment(DEV)) { ?>
    <div class="version_control_module">
        <div class="version_now" style="display: none;">
            <div class="close_open_set"></div>
            <div class="version_now_name">
                版本:<?php echo htmlspecialchars($_COOKIE['user_site_amodvis_laravel_version'] ?? '默认'); ?>
            </div>
            <div class="change_version"><a href="<?php echo UtilsCommon::urlAddCookieEdit(); ?>">版本切换</a></div>
        </div>
    </div>
    <script>
        $(".close_open_set").on("click", function () {
            closeOpenVersionSet.call($(this));
            var parent = $(this).parent();
            if (parent.hasClass('close')) {
                parent.addClass('open');
                parent.removeClass('close');
                $(this).html('>>');
                localStorage.setItem("close_open_set_value", "open");
            } else {
                parent.addClass('close');
                parent.removeClass('open');
                $(this).html('<<');
                localStorage.setItem("close_open_set_value", "close");
            }
        });

        function closeOpenVersionSet() {
            var close_open_set_value = localStorage.getItem("close_open_set_value");
            if (!close_open_set_value) {
                close_open_set_value = "open";
            }
            this.parent().addClass(close_open_set_value);
            this.parent().show();
            if (close_open_set_value == "open") {
                this.html(">>");
            } else {
                this.html("<<");
            }
        }

        closeOpenVersionSet.call($(".close_open_set"));
    </script>
<?php } ?>
