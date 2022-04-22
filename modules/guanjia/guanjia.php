<?php
/**
 * 搜外内容管家文章发布插件
 * @package 搜外内容管家
 * @link https://guanjia.seowhy.com/
 * @author Sinclair
 * @version 1.0.0
 */

defined('IN_PHPCMS') or exit('No permission resources.');

class guanjia
{

    private $config;

    public function __construct()
    {
        if ($_GET['a'] != 'client') {
            $config = $this->getConfig();
            if (empty($config['token'])) {
                $this->res(-1, "未配置成功");
            }
            $this->config = $config;
            $this->verifySign();
        }
    }

    public function client()
    {
        echo '<center>搜外内容管家接口</center>';
    }

    public function categories()
    {
        $modelId = $this->getModelIdByName('news');
        if (is_null($modelId)) {
            return array();
        }
        $model      = pc_base::load_model('category_model');
        $tmpCategories = $model->select('modelid=' . intval($modelId), 'catid,parentid,modelid,catname');
        $categories = array();
        foreach($tmpCategories as $val) {
            $categories[] = array(
                "id" => intval($val['catid']),
                "parent_id" => intval($val['parentid']),
                "title" => $val['catname'],
            );
        }

        $this->res(1, "获取分类成功", $categories);
    }

    public function publish()
    {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $categoryId = trim($_POST['category_id']);

        if (!$title || !$content || !$categoryId) {
            $this->res(-1, "发布文章失败，未提供标题或内容或分类ID");
        }
        
        $title   = $this->removeUtf8mb4($_POST['title']);
        $content = $this->removeUtf8mb4($_POST['content']);

        $modelId = $this->getModelIdByName('news');
        $model   = pc_base::load_app_class('guanjia_content_model', 'guanjia');
        $model->set_model($modelId);
        // Create index html
        define('RELATION_HTML', true);
        define('INDEX_HTML', true);

        $id = $model->add_content(array(
            'inputtime' => date('Y-m-d H:i:s'),
            'catid'     => $categoryId,
            'title'     => $title,
            'content'   => $content,
            'status'    => 99,
        ), 1);
        $model->set_model($modelId);
        $news  = $model->get_one(array('id' => $id));

        // 生成首页
        $this->updateIndexPage();

        $this->res(1, "发布成功", array(
            'url' => $news['url'],
        ));
    }

    public function upgrade() {
        // todo
    }

    private function updateIndexPage()
    {
        $html = pc_base::load_app_class('html', 'content');
        $html->index();
    }

    private function removeUtf8mb4($input)
    {
        $extensions = get_loaded_extensions();
        $output     = '';
        if (in_array('mbstring', $extensions)) {
            $length = mb_strlen($input, 'utf-8');
            for ($i = 0; $i < $length; $i++) {
                $char = mb_substr($input, $i, 1, 'utf-8');

                if (strlen($char) < 4) {
                    $output .= $char;
                }
            }
        } else if (in_array('iconv', $extensions)) {
            $length = iconv_strlen($input, 'utf-8');
            for ($i = 0; $i < $length; $i++) {
                $char = iconv_substr($input, $i, 1, 'utf-8');
                if (strlen($char) < 4) {
                    $output .= $char;
                }
            }
        }
        return $output;
    }

    private function getModelIdByName($name)
    {
        $model     = pc_base::load_model('sitemodel_model');
        $siteModel = $model->get_one(array('tablename' => $name));
        if (empty($siteModel)) {
            return null;
        }
        return $siteModel['modelid'];
    }

    private function getConfig()
    {
        if (empty($this->config)) {
            $this->config = getcache('guanjia_setting', 'guanjia');
            if (empty($this->config)) {
                $model                   = pc_base::load_model('module_model');
                $module                  = $model->get_one(array('module' => 'guanjia'));
                $this->config            = json_decode($module['setting'], true);
                $this->config['version'] = $module['version'];
                setcache('guanjia_setting', $this->config, 'guanjia');
            }
        }
        return $this->config;
    }

    private function setConfig($data)
    {
        $model        = pc_base::load_model('module_model');
        $module       = $model->get_one(array('module' => 'guanjia'));
        $this->config = $module['setting'] == '' ? array() : json_decode($module['setting'], true);
        $this->config = array_merge($this->config, $data);
        $model->update(array('setting' => json_encode($this->config)), array('module' => 'guanjia'));
        $this->config['version'] = $module['version'];
        setcache('guanjia_setting', $this->config, 'guanjia');
        return $this->config;
    }

    private function verifySign()
    {
        if (!isset($_GET['sign'])) {
            $this->res(-1, '未授权操作');
        }

        $sign      = $_GET['sign'];
        $checkTime  = $_GET['_t'];

        $config    = $this->getConfig();
        $signature = $this->signature($config['token'], $checkTime);
        if ($sign != $signature) {
            $this->res(-1, '签名不正确');
        }
        return $this;
    }
    
    private function signature($token, $_t)
    {
        $signature = md5($token . $_t);
        return $signature;
    }

    /**
     * json输出
     * @param      $code
     * @param null $msg
     * @param null $data
     * @param null $extra
     */
    public function res($code, $msg = null, $data = null, $extra = null)
    {
        @header('Content-Type:application/json;charset=UTF-8');
        if(is_array($msg)){
            $msg = implode(",", $msg);
        }
        $output = array(
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        );
        if (is_array($extra)) {
            foreach ($extra as $key => $val) {
                $output[$key] = $val;
            }
        }
        echo json_encode($output);
        die;
    }
}
