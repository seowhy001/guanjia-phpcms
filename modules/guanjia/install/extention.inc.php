<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$parent_menu = $menu_db->get_one(array('name' => 'content_publish'), 'id');
$parent_id   = $parent_menu['id'];

$id = $menu_db->insert(array('name' => 'guanjia', 'parentid' => $parent_id, 'm' => 'guanjia', 'c' => 'admin_guanjia', 'a' => 'index', 'data' => '', 'listorder' => 0, 'display' => '1'), true);

$language = array('guanjia' => '搜外内容管家');
pc_base::load_sys_func('dir');
dir_copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'languages', PC_PATH . 'languages');
?>