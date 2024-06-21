<?php (defined('BASEPATH')) or exit('No direct script access allowed');

function create_menus_options ($menus, $current){
    $options = '';
    foreach ($menus as $key => $each_menu) {

        $title = '';
        foreach ($each_menu->breadcrumbs as $j => $each_path){
            $title .= $each_path->title . ' / ';
        }

        $options .= '<option value="' . $each_menu->id . '"' . ($current && $each_menu->id == $current->id_parent ? ' selected' : '') . '' . ($current && $each_menu->id == $current->id ? ' disabled' : '') . '>' . $title . $each_menu->title . '</option>';

        if (isset($each_menu->children) && !empty($each_menu->children)){
            $children = create_menus_options($each_menu->children, $current);
            if ($children)
                $options .= $children;
        }
    }
    return $options;
}
