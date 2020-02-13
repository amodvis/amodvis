<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @yield('css')
    <style>
        .head, #page, .pagination_module, .foot {
            width: 1200px;
            margin: 0 auto;
        }

        .nav_list {
            margin-top: 20px;
        }

        .nav_list ul {
            overflow: hidden;
            text-align: center;
        }

        .nav_list ul li {
            line-height: 40px;
            background: #2769d0;
            width: 150px;
            display: inline-block;
        }

        .nav_list ul li.cur {
            background: #2b7af6;
        }

        .nav_list ul li {
            border-right: #1d5fc6 1px solid;
        }

        .nav_list ul li a {
            color: #ffffff;
        }

        .head .link_nav {
            width: auto;
        }

        .head .link_nav a {
            color: #1846dc;
        }

        .logo {
            background: url(http://106.54.93.177:9091/amodvis/static/image/19/3b/8e/193b8e8eef4f409880e7bb8fc8b15d5b.png) 0 0 no-repeat #4053dc;
            width: 160px;
            color: #fff;
            font-size: 16px;
            line-height: 60px;
            text-align: center;
            border-right: 1px solid #124490;
        }

        .logo a {
            color: #ffffff;
        }

        .logo a:hover {
            text-decoration: none;
        }

        .app_name .menu_trigger {
            border-bottom: 4px solid #0c5bea;
        }

        .bt_bd_nav {
            background: #fff;
            font-size: 12px;
            line-height: 24px;
            text-align: center;
            cursor: pointer;
            position: relative;
            color: #666;
            border-bottom: 4px solid #0c5bea;
            margin-left: 10px;
        }

        .app_name {
            border: none;
        }

        .admin_nav {
        }

        .app_name .menu_trigger {
            border-bottom: 4px solid #0c5bea;
            width: 50px;
            margin: 0 auto;
            color: #1846dc;
        }

        .app_name:hover .app_pop {
            display: block;
        }

        .app_name .app_pop {
            width: 110px;
            position: absolute;
            right: 0;
            top: 24px;
            display: none;
            z-index: 999;
            border: 1px solid #2769d0;
        }

        .app_name .app_pop a {
            color: #fff;
            display: block;
            background: #2b7af6;
        }

        .app_name .app_pop a:hover {
            color: #fff;
            display: block;
            background: #2769d0;
        }

        .app_name .app_pop a span {
            display: block;
            background: #fff;
            color: #1846dc;
        }

        .footer {
            text-align: center;
            color: #66a1fc;
            padding-bottom: 40px;
        }
    </style>
    <script>
        if (typeof webConfig === "undefined") {
            window.webConfig = {};
        }
    </script>
    @yield('head')
</head>
<body>
<div class="head">
    <div class="clearfix">
        <?php
        echo App\Widget\amodvis_admin\head_default\DefaultWidget::widget(['login_vendor_id' => $login_vendor_id ?? '']);
        ?>
    </div>
    <div class="nav_list">
        <?php
        $url_path = parse_url($_SERVER['REQUEST_URI']);
        $project_type_prefix = '';
        if (strstr($url_path["path"], 'a_')) {
            $project_type_prefix = 'a_';
        }
        if(!isset($not_show_main_nav)){
        $nav_arr = [
            ['link' => '/' . $project_type_prefix . 'pages_info/' . $app_name, 'add' => '', 'title' => '页面管理'],
            ['link' => '/' . $project_type_prefix . 'app_project_list/' . $app_name, 'add' => '', 'title' => '模块管理']
        ];
        ?>
        <ul>
            <?php
            foreach ($nav_arr as $nav) {
                $class = strstr($url_path["path"], $nav['link']) ? 'class="cur"' : '';
                $target_url = $nav['link'] . ($nav['add'] ?? '');
                echo '<li  ' . $class . '>
                <a href="' . $target_url . '">' . $nav['title'] . '</a>
            </li>';
            }
            ?>
        </ul>
        <?php
        }
        ?>
    </div>
</div>
@yield('content')
<div class="footer">
    design by fogetu
    <a href="https://github.com/amodvis/amodvis" style="color: #3582E1">wiki</a>
</div>
<?php
echo App\Widget\amodvis_admin\version_control\DefaultWidget::widget();
?>
</body>
</html>
