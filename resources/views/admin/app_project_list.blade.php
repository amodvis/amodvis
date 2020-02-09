@extends('layouts.app')
@section('title')
    <?php echo $title ?? '';?>
@endsection
@section('content')
    <?php
    $front_public_domain = config('common.amod_front_public_domain');
    $front_version = getFrontVersion();
    \App\Classes\Utils\FrontBuilder::pushCss($front_public_domain . 'laravle-amodvis/amodvis/css/adminshop/common.css?v=' . $front_version);
    ?>
    <div id="page">
        <h3>模块项目列表</h3>
        <ul class="project_list">
            <li><a href="/modules_info/<?php echo $app_name;?>/amodvis_company">amodvis_company</a></li>
            <li><a href="/modules_info/<?php echo $app_name;?>/home_company">home_company</a></li>
            <li><a href="/modules_info/<?php echo $app_name;?>/public_project">public_project</a></li>
        </ul>
    </div>
@endsection
@section('css')
    <?php
    echo \App\Classes\Utils\FrontBuilder::showAllCss();
    ?>
    <style>
        .project_list {
            overflow: hidden;
            min-height: 300px;
            margin-top: 40px;
        }

        .project_list li {
            width: 160px;
            height: 40px;
            text-align: center;
            line-height: 40px;
            float: left;
            background: #f6f6f6;
            margin-right: 10px;
        }

        .project_list li a {
            color: #888;
        }
    </style>
@endsection
