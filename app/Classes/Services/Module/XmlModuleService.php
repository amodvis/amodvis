<?php

namespace App\Classes\Services\Module;

class XmlModuleService
{
    public static $textIndex = 0;

    private function evalFormItem($node_value, $data, $form_name_str)
    {
        if (isset($data[$form_name_str])) {
            $node_value = $data[$form_name_str];
        } elseif (preg_match('/([a-zA-Z0-9_]+)(\[[0-9]+\])\[([0-9-a-zA-Z_]+)\]/', $form_name_str, $match)) {
            eval('$node_value = $data[\'' . $match[1] . '\']' . $match[2] . '[\'' . $match[3] . '\']??\'\';');
        } elseif (preg_match('/([a-zA-Z0-9_]+)\[([a-zA-Z_]+[0-9-a-zA-Z_]+)\]/', $form_name_str, $match)) {
            eval('$node_value = $data[\'' . $match[1] . '\'][\'' . $match[2] . '\']??\'\';');
        } elseif (preg_match('/([a-zA-Z0-9_]+)\[\]/', $form_name_str, $match)) {
            $node_value = $data[$match[1]][self::$textIndex] ?? '';
            self::$textIndex++;
        }
        return $node_value;
    }


    private function radioComponent($item, $form_html_str, $data, $name)
    {
        $form_html_str .= '<!-- 组件按钮样式开始-->';
        $options = $item->getElementsByTagName('option');
        $options_len = $options->length;
        for ($k = 0; $k < $options_len; $k++) { //遍历标签

            $item = $options->item($k); //获取列表中单条记录
            if ($item->getAttribute('selected') === 'selected') {
                $selectedString = 'checked="checked"';
            } else {
                $selectedString = '';
            }
            if (isset($data[$name])) {
                if ($data[$name] == $item->getAttribute('value')) {
                    $selectedString = 'checked="checked"';
                } else {
                    $selectedString = '';
                }
            }
            $form_html_str .= '' .
                '<label><input class="input-radio" ' . $selectedString . ' type="radio" value="' . $item->getAttribute('value') . '" name="' . $name . '"/>' . trim($item->nodeValue) . '</label> ' .
                '';
        }
        $form_html_str .= '<!-- 组件按钮样式结束-->';
        return $form_html_str;
    }

    private function checkboxComponent($item, $form_html_str, $data, $name)
    {
        $form_html_str .= '<!-- 组件按钮样式开始-->';
        $options = $item->getElementsByTagName('option');
        $options_len = $options->length;
        $checkArr = $data[$name] ?? [];
        for ($k = 0; $k < $options_len; $k++) { //遍历标签
            $item = $options->item($k); //获取列表中单条记录
            if ($item->getAttribute('selected') === 'selected' && empty($data)) {
                $selectedString = 'checked="checked"';
            } else {
                $selectedString = '';
            }
            if (isset($data[$name])) {

                if (in_array($item->getAttribute('value'), $checkArr)) {
                    $selectedString = 'checked="checked"';
                } else {
                    $selectedString = '';
                }
            }
            $form_html_str .= '<label><input class="input-radio" ' . $selectedString . ' type="checkbox"
                                     value="' . $item->getAttribute('value') . '" name="' . $name . '[]"/>' . trim($item->nodeValue) . '</label> ';
            return $form_html_str;
        }
    }

    private function colorComponent($item, $form_html_str, $data, $name, $readonly = false)
    {
        $form_html_str .= '<!-- select选择框样式开始-->';
        $options = $item->getElementsByTagName('option');
        $options_len = $options->length;
        $flag = false;
        if (isset($data[$name]) && true == $readonly) {
            $flag = true;
        }
        if (false === $flag) {
            $form_html_str .= '<ul  class="color_component">';
        }
        $final_node_value = '';
        $final_value = '';
        for ($k = 0; $k < $options_len; $k++) { //遍历标签
            $item = $options->item($k); //获取列表中单条记录
            $node_value = trim($item->nodeValue);
            $node_value = $this->evalFormItem($node_value, $data, $name);
            if ($node_value === $item->getAttribute('value')) {
                $selectedString = 'checked';
                $final_value = $node_value;
                $final_node_value = $item->textContent;
            } else {
                $selectedString = '';
            }
            if (false === $flag) {
                $form_html_str .= '<li style="" 
                class="' . $selectedString . '" data-value="' . $item->getAttribute('value') . '" 
                title="' . $item->textContent . '">
                        <div class="inner_box" style="background:' . $item->getAttribute('value') . '"></div>                
                    </li>';
            }
        }
        if (false === $flag) {
            $form_html_str .= '</ul>' .
                '<!-- select选择框样式结束 -->';
            $form_html_str .= '<input type="hidden" value="' . $final_value . '" name="' . $name . '" autocomplete="off" class="input-box">';
        } else {
            $form_html_str .= $final_node_value . '<input type="hidden" value="' . $final_value . '" name="' . $name . '" autocomplete="off" class="input-box">';
        }
        return $form_html_str;
    }

    private function selectComponent($item, $form_html_str, $data, $name, $readonly = false)
    {
        $form_html_str .= '<!-- select选择框样式开始-->';
        $options = $item->getElementsByTagName('option');
        $options_len = $options->length;
        $flag = false;
        if (isset($data[$name]) && true == $readonly) {
            $flag = true;
        }
        if (false === $flag) {
            $form_html_str .= '<select name="' . $name . '" class="input-box">';
        }
        $final_node_value = '';
        $final_value = '';
        for ($k = 0; $k < $options_len; $k++) { //遍历标签
            $item = $options->item($k); //获取列表中单条记录
            $node_value = trim($item->nodeValue);
            $node_value = $this->evalFormItem($node_value, $data, $name);
            if ($node_value === $item->getAttribute('value')) {
                $selectedString = 'selected="selected"';
                $final_value = $node_value;
                $final_node_value = $item->textContent;
                if (true === $flag) {
                    $form_html_str .= $final_node_value . '<input type="hidden" value="' . $final_value . '" name="' . $name . '" autocomplete="off" class="input-box">';
                    return $form_html_str;
                }
            } else {
                $selectedString = '';
            }
            if (false === $flag) {
                $form_html_str .= '<option ' . $selectedString . ' value="' . $item->getAttribute('value') . '">' . $item->textContent . '</option>';
            }
        }
        if (false === $flag) {
            $form_html_str .= '</select>' .
                '<!-- select选择框样式结束 -->';
        } else {
            $form_html_str .= $final_node_value . '<input type="hidden" value="' . $final_value . '" name="' . $name . '" autocomplete="off" class="input-box">';
        }
        return $form_html_str;
    }

    private function textareaComponent($item, $form_html_str, $data, $name)
    {
        $verify_string = '';
        $add_class = '';
        if ($item->getAttribute('verify')) {
            $verify_string = 'data-verify="' . $item->getAttribute('verify') . '"';
            $add_class = 'J_BVerify';
        }
        $node_value = $item->nodeValue;
        $node_value = $this->evalFormItem($node_value, $data, $item->getAttribute('name'));
        $form_html_str .= '<!-- textarea输入框样式开始-->' .
            '<textarea ' . $verify_string . ' class="' . $add_class . '" style="resize:both;" name="' . $name . '" rows="10" cols="30">' . trim($node_value) . '</textarea>' .
            '<!-- textarea输入框样式结束-->';
        return $form_html_str;
    }

    private function hiddenComponent($item, $form_html_str, $data, $name, $readonly = false)
    {
        $node_value = $item->nodeValue;
        $node_value = $this->evalFormItem($node_value, $data, $name);
        $verify_string = '';
        $add_class = '';
        if ($item->getAttribute('verify')) {
            $verify_string = 'data-verify="' . $item->getAttribute('verify') . '"';
            $add_class = 'J_BVerify';
        }
        $node_value = trim($node_value);
        if ((false === $readonly || empty($data)) || !$node_value) {
            $form_html_str .= '<!-- text输入框样式开始 -->' .
                '<input ' . $verify_string . ' type="hidden" value="' . $node_value . '" name="' . $name . '" autocomplete="off" class="input-box ' . $add_class . '">' .
                '<!-- text输入框样式结束-->';
        } else {
            $form_html_str .= $node_value . '<input type="hidden" value="' . $node_value . '" name="' . $item->getAttribute('name') . '" autocomplete="off" class="input-box">';
        }
        return $form_html_str;
    }

    private function textComponent($item, $form_html_str, $data, $name, $readonly = false)
    {
        $node_value = $item->nodeValue;
        $node_value = $this->evalFormItem($node_value, $data, $name);
        $verify_string = '';
        $add_class = '';
        if ($item->getAttribute('verify')) {
            $verify_string = 'data-verify="' . $item->getAttribute('verify') . '"';
            $add_class = 'J_BVerify';
        }
        $node_value = trim($node_value);
        if ((false === $readonly || empty($data)) || !$node_value) {
            $form_html_str .= '<!-- text输入框样式开始 -->' .
                '<input ' . $verify_string . ' type="text" value="' . $node_value . '" name="' . $name . '" autocomplete="off" class="input-box ' . $add_class . '">' .
                '<!-- text输入框样式结束-->';
        } else {
            $form_html_str .= $node_value . '<input type="hidden" value="' . $node_value . '" name="' . $item->getAttribute('name') . '" autocomplete="off" class="input-box">';
        }
        return $form_html_str;
    }

    private function showTitleComponent($item, $form_html_str, $data, $name)
    {
        $node_value = $item->nodeValue;
        $node_value = $this->evalFormItem($node_value, $data, $name);
        $node_show_value = $this->evalFormItem($node_value, $data, '__' . $name);
        if (trim($node_show_value)) {
            $checkString1 = "";
            $checkString2 = 'checked="checked"';
            $title_class = 'display:inline;';
        } else {
            $checkString1 = 'checked="checked"';
            $checkString2 = '';
            $title_class = 'display:none;';
        }
        $form_html_str .= '<label>
                                    <input type="radio" class="J_TNotShowTitle  input-radio" name="__' . $name . '" value=0 ' . $checkString1 . '>否
                                </label>
                                <label>
                                    <input type="radio" class="J_TShowTitle  show-title-true input-radio" name="__' . $name . '" value=1  ' . $checkString2 . '>是
                                </label>
                                <input type="text" style="' . $title_class . '" class="J_TTitleInput input-box title-input" maxlength="30" name="' . $name . '" value="' . trim($node_value) . '">';
        return $form_html_str;
    }


    private function itemComponent($item, $form_html_str, $name)
    {
        $form_html_str .= '<!-- 组件按钮样式开始 -->
                                <a data-trigger="' . $name . '" data_show_setting="' . $item->getAttribute('show_setting') . '" class="btn-ok J_ItemChooseBtn" href="javascript:void(0);">选择宝贝</a>
                                <input type="hidden" name="item_exists" value=1>
                                <!-- 组件按钮样式结束 -->';
        return $form_html_str;
    }

    private function dateTimeComponent($item, $form_html_str, $data, $name)
    {
        $node_value = $item->nodeValue;
        $node_value = $this->evalFormItem($node_value, $data, $name);
        $form_html_str .= '<!-- date_time输入框样式开始 -->' .
            '<input type="text" value="' . trim($node_value) . '" name="' . $name . '" autocomplete="off" class="input-box J_CalendarTimeTrigger">' .
            '<!-- date_time输入框样式结束-->';
        return $form_html_str;
    }

    private function dateTimeMonthComponent($item, $form_html_str, $data, $name)
    {
        $node_value = $item->nodeValue;
        if (isset($data[$name])) {
            $node_value = $data[$name];
            if (is_numeric($node_value)) {
                $node_value = date("Y-m-d H:i:s", $node_value);
            }
        }
        $form_html_str .= '<!-- text输入框样式开始 -->' .
            '<input type="text" value="' . trim($node_value) . '" name="' . $name . '" autocomplete="off" class="input-box J_DatetimepickerTrigger">' .
            '<!-- text输入框样式结束-->';
        return $form_html_str;
    }

    private function warmComponent($item, $form_html_str, $data, $name)
    {
        $node_value = $item->nodeValue;
        $node_value = $this->evalFormItem($node_value, $data, $name);
        $form_html_str .= '<!-- 提示 -->' .
            '<span class="warm_text">' . $node_value . '</span>' .
            '<!-- 提示-->';
        return $form_html_str;
    }

    private function fileComponent($item, $form_html_str, $data, $name)
    {
        $node_value = $item->nodeValue;
        $node_value = $this->evalFormItem($node_value, $data, $name);
        if ($node_value) {
            $valString = 'value=' . $node_value . '';
        } else {
            $valString = "";
        }
        $form_html_str .= '<!-- 组件样式开始 -->
                                <input type="text" ' . $valString . ' name="' . $name . '"  class="input-box">
                                <a data-trigger="' . $name . '" class="btn-ok J_FileChooseBtn" href="javascript:void(0);">去素材库选择</a>
                                <!-- 组件按钮样式结束 -->';
        return $form_html_str;
    }

    private function layoutComponent($item, $form_html_str, $data, $name, $debug_routes)
    {
        $node_value = $item->nodeValue;
        $node_value = $this->evalFormItem($node_value, $data, $name);
        $part_modules = $node_value_json = $node_value ? json_decode($node_value, true) : [];
        if ($debug_routes) {
            $form_html_str .= '
                                    <ul class="nav clear-fix" role="tablist">
                                    <li class="first selected"><span>默认</span></li>
                                    <li class=""><span>源码</span></li>
                                    </ul>';
        }
        $form_html_str .= '<div class="panels component_layout">';
        $form_html_str .= '<div class="panel item-set J_SetLayoutPop">';
        $is_has_main_layout = false;
        $is_has_sub_layout = false;
        foreach ($part_modules as $index => $items) {
            if (!empty($items['main'])) {
                $is_has_main_layout = true;
            }
            if (isset($items['sub_min']) || isset($items['sub_max'])) {
                $is_has_sub_layout = true;
            }
        }
        if (false == $is_has_main_layout) {
            $main_flag = false;
            foreach ($part_modules as $type_key => $items) {
                foreach ($items as $hole_type => $json_items) {
                    if ($hole_type === 'main') {
                        $main_flag = true;
                    }

                }
            }
            if (false === $main_flag) {
                $form_html_str .= '<div class="layout_main" data_hole_type="main">
                                                   <div class="module_item J_ModuleItem J_TEmptyBox">无模块</div>
                                                    <div class="add_one_module_layout">新增</div>
                                            </div>';
            }
        }
        foreach ($part_modules as $type_key => $items) {
            if (count($items) > 1) {
                $form_html_str .= '<div class="layout_part clear-fix">';
            }
            foreach ($items as $hole_type => $json_items) {
                $form_html_str .= '<div class="layout_' . $hole_type . ' J_TRegion" data_hole_type="' . $hole_type . '">';
                if (empty($json_items)) {
                    $form_html_str .= '<div class="module_item J_ModuleItem J_TEmptyBox">无模块</div>';
                }
                foreach ($json_items as $json_item) {

                    $module_info = app(ModuleService::class)->getModuleInfoByCache($json_item['project_name'], $json_item['module_name']);
                    if (empty($module_info)) {
                        $nick_name = $json_item['module_name'];
                    } else {
                        $nick_name = $module_info->nick_name;
                    }
                    $form_html_str .= '<div class="module_item J_ModuleItem"><table class="dataintable">
                                                    <tr class="hidden">
                                                    <td><input style="width:100%;" type="text" placeholder="project_name" value="' . $json_item['project_name'] . '" name="" autocomplete="off" class="input-box input_project_name"></td>
                                                    <td><input style="width:100%;" type="text" placeholder="module_name" value="' . $json_item['module_name'] . '" name="" autocomplete="off" class="input-box input_module_name"></td>
                                                    <td><input style="width:100%;" type="text" placeholder="page_name" value="' . $json_item['page_name'] . '" name="" autocomplete="off" class="input-box input_page_name"></td>
                                                    <td><input style="width:100%;" type="text" placeholder="position" value="' . $json_item['position'] . '" name="" autocomplete="off" class="input-box input_position"></td>
                                                    </tr>
                                                    <tr> 
                                                     <td colspan="4">
                                                        <div class="module_user_control">
                                                        <div class="module_text">' . $nick_name . '</div>
                                                        <div class="module_del J_ModuleDel">X</div>
                                                        </div>
                                                     </td>
                                                    </tr>
                                                    </table></div>';
                }
                $form_html_str .= '<div class="add_one_module_layout">新增</div>';
                $form_html_str .= '</div>';
            }
            if (count($items) > 1) {
                $form_html_str .= "</div>";
            }
        }
        if (false === $is_has_sub_layout) {
            $form_html_str .= '<div class="layout_part clear-fix"><div class="layout_sub_min" data_hole_type="sub_min">
                                                    <div class="module_item J_ModuleItem J_ModuleItem"></div>
                                                    <div class="add_one_module_layout">新增</div>
                                            </div><div class="layout_sub_max" data_hole_type="sub_max">
                                                <div class="module_item J_ModuleItem J_ModuleItem"></div>
                                                    <div class="add_one_module_layout">新增</div>
                                            </div></div>';
        }
        $form_html_str .= '</div>';
        $form_html_str .= '<div class="panel item-set hidden">';
        $node_value = json_encode($node_value_json, JSON_PRETTY_PRINT);
        $form_html_str .= '<!-- textarea输入框样式开始-->' .
            '<textarea style="resize:both;" name="' . $item->getAttribute('name') . '" rows="10" cols="30">' . $node_value . '</textarea>' .
            '<!-- textarea输入框样式结束-->';
        $form_html_str .= '</div>';
        $form_html_str .= '</div>';
        return $form_html_str;
    }


    private function htmlEditComponent($item, $form_html_str, $data, $name)
    {
        $node_value = $item->nodeValue;
        $node_value = $this->evalFormItem($node_value, $data, $name);
        if ($node_value) {
            $valString = preg_replace('/\n||\r/', "", addslashes($node_value));;
        } else {
            $valString = "";
        }
        $form_html_str .= '<!-- 组件按钮样式开始 -->
                                <a data-trigger="' . $name . '" class="btn-ok J_HtmlEditor" href="javascript:void(0);">html编辑器</a>
                                  <textarea name="' . $name . '" style="visibility:hidden;width:1px;height:1px;">' . htmlspecialchars(stripslashes($valString)) . '</textarea>
                                  <!-- 组件按钮样式结束 -->';
        return $form_html_str;
    }

    public function getModuleEditHtml($module_type_name, $action, $project_name, $module_name, $form_data)
    {
        $amv_path = AMVPHP_PATH;
        if ('react' === $module_type_name) {
            $amv_path = AMV_PATH;
        }
        $xml_path = $amv_path . '/' . $project_name . '/' . $module_name . '/module.xml';
        $xmlstring = file_get_contents($xml_path);
        $actionBase = '';
        $html = $this->xmlTable($xmlstring, $action, $actionBase, $form_data);
        return app('app.response')->arrSuccess($html);
    }


    public function getModuleProductEditHtml($module_type_name, $action, $project_name, $module_name, $form_data)
    {
        $amv_path = AMVPHP_PATH;
        if ('react' === $module_type_name) {
            $amv_path = AMV_PATH;
        }
        $xml_path = $amv_path . '/' . $project_name . '/' . $module_name . '/product_module.xml';
        $xmlstring = file_get_contents($xml_path);
        $actionBase = '';
        $html = $this->xmlTable($xmlstring, $action, $actionBase, $form_data);
        return app('app.response')->arrSuccess($html);
    }

    public function xmlTable($xmlstring, $action, $actionBase, $data)
    {
        self::$textIndex = 0;
        $dom = new \DOMDocument();
        $dom->loadXML($xmlstring);
        if (request()->input('debug_routes')) {
            $debug_routes = 1;
        } else {
            $debug_routes = 0;
        }
        $itemList = $dom->getElementsByTagName('group');
        $len = $itemList->length;
        $htmlString = '';
        $htmlString .= '<div class="body" id="module_edit">';
        $htmlString .= '<div class="editPanel h_auto">' .
            '<form method="POST" name="moduleSetForm" class="auto-rec-form form-default" action="' . $action . '"   action-base="' . $actionBase . '"  autocomplete="off">' .
            '<div class="tab">';
        $tem_htmlString = '';
        for ($i = 0; $i < $len; $i++) { //遍历标签
            $item = $itemList->item($i); //获取列表中单条记录
            $item_title = $item->getAttribute('title'); //获取属性值
            if ($i === 0) {
                $selectedCls = 'selected first';
            } else {
                $selectedCls = '';
            }
            if ($item_title) {
                $tem_htmlString .= ' <li class="' . $selectedCls . '"><span>' . $item_title . '</span></li>';
            }
        }
        if ($tem_htmlString) {
            $htmlString .= '<ul class="nav clear-fix" role="tablist">';
            $htmlString .= $tem_htmlString;
            $htmlString .= '</ul>';
        } else {
            $htmlString .= '<div class="lh30">&nbsp;</div>';
        }


        $htmlString .= '<div class="panels">';
        for ($i = 0; $i < $len; $i++) { //遍历标签
            $item = $itemList->item($i); //获取列表中单条记录

            $section = $item->getElementsByTagName('section');
            $sec_len = $section->length;

            if ($i === 0) {
                $selectedCls = '';
            } else {
                $selectedCls = 'hidden';
            }
            $htmlString .= '<div class="panel item-set ' . $selectedCls . '">';
            for ($n = 0; $n < $sec_len; $n++) { //遍历标签

                $item = $section->item($n); //获取列表中单条记录


                $sectonTitle = $item->getAttribute('title');
                if ($sectonTitle) {
                    $sectonFolded = $item->getAttribute('folded');
                    if ($sectonFolded == 'true') {
                        $sectonString = 'closed';
                    } else {
                        $sectonString = '';
                    }
                    $htmlString .= '<div class="items-set ' . $sectonString . '">' .
                        '<div class="title">' .
                        '<h3 class="J_itemSetTrigger"><i></i>' . $sectonTitle . '</h3>' .
                        '<div class="pin_icon"><span></span></div>' .
                        '</div>' .
                        '<div class="set-inner">';
                }

                $param = $item->getElementsByTagName('param');
                $param_len = $param->length;

                for ($m = 0; $m < $param_len; $m++) { //遍历标签

                    $item = $param->item($m); //获取列表中单条记录
                    $name = $item->getAttribute('name');
                    $formType = $item->getAttribute('formType'); //获取属性值
                    $form_item_class = '';
                    if ($formType === 'hidden') {
                        $form_item_class = 'hidden';
                    }
                    $htmlString .= '<div class="control-group ' . $form_item_class . '">' .
                        '<label for="" class="control-label"> ' . $item->getAttribute('label') . '</label>';
                    $readonly = 'true' === $item->getAttribute('readonly') ? true : false; //获取属性值
                    $form_html_str = '';
                    $control_class = '';
                    switch ($formType) {
                        case 'color':
                            $form_html_str = $this->colorComponent($item, $form_html_str, $data, $name, $readonly);
                            break;
                        case 'radio':
                            $form_html_str = $this->radioComponent($item, $form_html_str, $data, $name);
                            break;
                        case 'checkbox':
                            $form_html_str = $this->checkboxComponent($item, $form_html_str, $data, $name);
                            break;
                        case 'select':
                            $form_html_str = $this->selectComponent($item, $form_html_str, $data, $name, $readonly);
                            break;
                        case 'textarea':
                            $form_html_str = $this->textareaComponent($item, $form_html_str, $data, $name);
                            break;
                        case 'hidden':
                            $form_html_str = $this->hiddenComponent($item, $form_html_str, $data, $name, $readonly);
                            break;
                        case 'text':
                            $form_html_str = $this->textComponent($item, $form_html_str, $data, $name, $readonly);
                            break;
                        case 'item':
                            $form_html_str = $this->itemComponent($item, $form_html_str, $name);
                            break;
                        case 'show_title':
                            $form_html_str = $this->showTitleComponent($item, $form_html_str, $data, $name);
                            break;
                        case 'date_time':
                            $form_html_str = $this->dateTimeComponent($item, $form_html_str, $data, $name);
                            break;
                        case 'date_time_month':
                            $form_html_str = $this->dateTimeMonthComponent($item, $form_html_str, $data, $name);
                            break;
                        case 'warm':
                            $form_html_str = $this->warmComponent($item, $form_html_str, $data, $name);
                            break;
                        case 'image':
                            $form_html_str = $this->fileComponent($item, $form_html_str, $data, $name);
                            break;
                        case 'html_editor':
                            $form_html_str = $this->htmlEditComponent($item, $form_html_str, $data, $name);
                            break;
                        case 'layout':
                            $form_html_str = $this->layoutComponent($item, $form_html_str, $data, $name, $debug_routes);
                            break;
                        default:
                            $form_html_str = $this->textComponent($item, $form_html_str, $data, $name, $readonly);
                            break;
                    }
                    if ("true" !== $readonly || empty($data)) {
                        $form_html_str .= '<span class="tips">' . $item->getAttribute('description') . '</span>';
                    }
                    $htmlString .= '<div class="control ' . $control_class . '">' . $form_html_str . '</div>' .
                        '</div>';
                }
                if ($sectonTitle) {
                    $htmlString .= '</div>' .
                        '</div>';
                }
            }
            $htmlString .= '</div>';
        }
        $htmlString .= '</div>';
        $htmlString .= '<div class="opt-footer" style="visibility: hidden;">
							<input type="button" value="保存" class="btn-ok J_Btn-ok">
						</div>';

        $htmlString .= '</div>' .
            '</form>' .
            '</div>' .
            '</div>';
        return $htmlString;
    }
}
