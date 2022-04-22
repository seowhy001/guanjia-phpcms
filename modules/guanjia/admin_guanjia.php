<?php
/**
 * 
 * @author https://guanjia.seowhy.com/
 * @description 搜外内容管家文章发布插件
 * @package 搜外内容管家
 * @version 1.0.0
 */

defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);

class admin_guanjia extends admin {

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if(isset($_POST['dosubmit'])) {
            $token = isset($_POST['token']) ? trim($_POST['token']) : '';
            if(empty($token)) {
                showmessage("请到 https://guanjia.seowhy.com 获取Token", HTTP_REFERER);
            }

            $setting = array('token' => $token);
            $module_db = pc_base::load_model('module_model');
            $module_db->update(array('setting' => json_encode($setting)), array('module' => 'guanjia'));

            $module = $module_db->get_one(array('module' => 'guanjia'));

            $setting['version'] = $module['version'];
            setcache('guanjia_setting', $setting, 'guanjia');

            showmessage('保存成功', HTTP_REFERER);
        } else {
            $response = getcache('guanjia_setting', 'guanjia');

            if(empty($response)) {
                $module_db = pc_base::load_model('module_model');
                $module = $module_db->get_one(array('module' => 'guanjia'));
                $response['version'] = $module['version'];
                $setting = json_encode($module['setting'], true);
                $response = array_merge($response, $setting);
            }
            $response['url'] = APP_PATH . 'index.php?m=guanjia&c=guanjia&a=client';
            include $this->admin_tpl('guanjia_setting');
        }
    }
}
?>