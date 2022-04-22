<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('UNINSTALL') or exit('Access Denied');

$menu_db = pc_base::load_model('menu_model');
$menu_id = $menu_db->delete(array('name' => 'guanjia'));

pc_base::load_sys_func('dir');
$langPath = PC_PATH . implode(DIRECTORY_SEPARATOR, array('modules', 'guanjia', 'install', 'languages'));
$langFiles = dir_list($langPath);
foreach ($langFiles as $file) {
    if(is_file($file)) {
        @unlink(PC_PATH . 'languages' . substr($file, -(strlen($file) - strlen($langPath))));
    }
}

delcache('guanjia_setting', 'guanjia');
?>
