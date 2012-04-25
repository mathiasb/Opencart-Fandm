<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

require_once (dirname(__FILE__) . '/magictoolbox/module.php');

$tool = & magicslideshow_load_core_class();

class ControllerModuleMagicSlideshow extends Controller {
    private $error = array();
    private $params = array();

    public function index () {

            $tool = $GLOBALS["magictoolbox"]["magicslideshow"];
            $shop_dir = str_replace('system/', '', DIR_SYSTEM);
            $image_dir = str_replace($shop_dir, '', DIR_IMAGE);
            $pathToCache = '/'.$image_dir.'magictoolbox_cache';

            /*STANDARD CODE*/
            $this->load->language('module/magicslideshow'); // load lang. file

            /*$this->document->title = $this->language->get('heading_title'); //load title*/

            $this->load->model('setting/setting');//just include file admin/model/setting/setting.php and create object ModelSettingSetting

            $token = isset($this->session->data['token'])? '&token='.$this->session->data['token']:'';

            if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
                if(isset($this->request->post['clear_cache']) && $this->request->post['clear_cache'] == '1') {
                    //clear cache
                    $this->params = $this->model_setting_setting->getSetting('magicslideshow');//load settings from DB
                    foreach ($tool->params->getArray() as $param => $values) {
                        if (isset($this->params[$values['id']])) {
                            $tool->params->set($values['id'],$this->params[$values['id']]);
                        }
                    }
                    require_once(dirname(__FILE__) . '/magictoolbox/magictoolbox.imagehelper.class.php');
                    $imagehelper = new MagicToolboxImageHelperClass($shop_dir, $pathToCache, $tool->params);
                    $usedSubCache = $imagehelper->getOptionsHash();
                    if(is_dir($shop_dir.$pathToCache))
                        $this->clearCache($shop_dir.$pathToCache, ($this->request->post['what-clear'] == 'all_items')?null:$usedSubCache);
                } else {
                    //save params
                    unset($this->request->post['clear_cache']);
                    unset($this->request->post['what-clear']);
                    $this->model_setting_setting->editSetting('magicslideshow', $this->request->post);
                }
                $this->session->data['success'] = $this->language->get('text_success');
                $this->redirect(HTTPS_SERVER . 'index.php?route=extension/module' . $token);
            }

            $this->data['heading_title'] = $this->language->get('heading_title');

            $this->data['text_enabled'] = $this->language->get('text_enabled');
            $this->data['text_disabled'] = $this->language->get('text_disabled');

            $this->data['entry_status'] = $this->language->get('entry_status');

            $this->data['button_save'] = $this->language->get('button_save');
            $this->data['button_cancel'] = $this->language->get('button_cancel');

            $this->data['button_clear'] = $this->language->get('button_clear');

            $this->params = $this->model_setting_setting->getSetting('magicslideshow');//load settings from DB

            foreach ($tool->params->getArray() as $param => $values) {
                if (isset($this->params[$values['id']])) {
                    $tool->params->set($values['id'],$this->params[$values['id']]);
                }
            }

            require_once(dirname(__FILE__) . '/magictoolbox/magictoolbox.imagehelper.class.php');
            $imagehelper = new MagicToolboxImageHelperClass($shop_dir, $pathToCache, $tool->params);
            $usedSubCache = $imagehelper->getOptionsHash();
            $cacheInfo = $this->getCacheInfo($shop_dir.$pathToCache, $usedSubCache);
            $this->data['path_to_cache'] = $pathToCache;
            $this->data['total_items'] = $cacheInfo['totalCount'].' ('.$this->format_size($cacheInfo['totalSize']).')';
            $this->data['unused_items'] = $cacheInfo['unusedCount'].' ('.$this->format_size($cacheInfo['unusedSize']).')';

            $this->data['parameters'] = $tool->params->getArray(); // LOAD PARAMS

            if (isset($this->error['warning'])) {
                    $this->data['error_warning'] = $this->error['warning'];
            } else {
                    $this->data['error_warning'] = '';
            }

            if (isset($this->error['code'])) {
                    $this->data['error_code'] = $this->error['code'];
            } else {
                    $this->data['error_code'] = '';
            }

            $this->data['breadcrumbs'] = array();

            $this->data['breadcrumbs'][] = array(
            'href'      => HTTPS_SERVER . 'index.php?route=common/home' . $token,
            'text'      => $this->language->get('text_home'),
            'separator' => FALSE
            );

            $this->data['breadcrumbs'][] = array(
            'href'      => HTTPS_SERVER . 'index.php?route=extension/module' . $token,
            'text'      => $this->language->get('text_module'),
            'separator' => ' :: '
            );

            $this->data['breadcrumbs'][] = array(
            'href'      => HTTPS_SERVER . 'index.php?route=module/magicslideshow' . $token,
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: '
            );

            $this->data['action'] = HTTPS_SERVER . 'index.php?route=module/magicslideshow' . $token;

            $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/module' . $token;

            if (isset($this->request->post['magicslideshow_status'])) {
                    $this->data['magicslideshow_status'] = $this->request->post['magicslideshow_status'];
            } else {
                    $this->data['magicslideshow_status'] = $this->config->get('magicslideshow_status');
            }

            $this->template = 'module/magicslideshow.tpl';
            $this->children = array(
                    'common/header',
                    'common/footer'
            );

            $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function validate() {
            if (!$this->user->hasPermission('modify', 'module/magicslideshow')) {
                    $this->error['warning'] = $this->language->get('error_permission');
            }

            if (!$this->error) {
                    return TRUE;
            } else {
                    return FALSE;
            }
    }

    private function clearCache($path, $usedSubCache = null) {
        $files = glob($path.DIRECTORY_SEPARATOR.'*');
        if($files !== FALSE && !empty($files)) {
            foreach($files as $file) {
                if(is_dir($file)) {
                    if(!$usedSubCache || $usedSubCache != substr($file, strrpos($file, DIRECTORY_SEPARATOR)+1)) {
                        $this->clearCache($file);
                        @rmdir($file);
                    }
                } else {
                    @unlink($file);
                }
            }
        }
        return;
    }

    function getCacheInfo($path, $usedSubCache = null) {

        $totalSize = 0;
        $totalCount = 0;
        $usedSize = 0;
        $usedCount = 0;
        if (is_dir($path))
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                $next = $path . DIRECTORY_SEPARATOR . $file;
                if ($file != '.' && $file != '..' && !is_link($next)) {
                    if (is_dir($next)) {
                        $result = $this->getCacheInfo($next);
                        if($file == $usedSubCache) {
                            $usedSize += $result['totalSize'];
                            $usedCount += $result['totalCount'];
                        }
                        $totalSize += $result['totalSize'];
                        $totalCount += $result['totalCount'];
                    } elseif (is_file($next)) {
                        $totalSize += filesize($next);
                        $totalCount++;
                    }
                }
            }
            closedir($handle);
        }
        return array('totalSize' => $totalSize, 'totalCount' => $totalCount, 'unusedSize' => $totalSize-$usedSize, 'unusedCount' => $totalCount-$usedCount);
    }

    function format_size($size) {
        $units = array(' bytes', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return round($size, 2).$units[$i];
    }

}
?>