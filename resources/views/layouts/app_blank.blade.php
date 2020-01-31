<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @yield('css')
    <script>
        if (typeof webConfig === "undefined") {
            window.webConfig = {};
        }
    </script>
    @yield('head')
</head>
<body>
@yield('content')
<?php
echo App\Widget\amodvis_admin\version_control\DefaultWidget::widget();
?>
</body>
</html>
