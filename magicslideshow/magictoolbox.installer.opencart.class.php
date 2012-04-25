<?php
    /**
        OpenCart module installer class
    */

    require_once(dirname(__FILE__) . '/magictoolbox.installer.core.class.php');
	include(dirname(__FILE__).'/../config.php');
	if (defined("HTTP_SERVER") && constant("HTTP_SERVER") && defined("HTTP_ADMIN") && constant("HTTP_ADMIN")) {
		$admin_folder_name = str_replace('/','',(str_replace(HTTP_SERVER,'',HTTP_ADMIN)));
	} else {
		$admin_folder_name = 'admin';
	}

    class MagicToolboxOpencartModuleInstallerClass extends MagicToolboxCoreInstallerClass {

        function MagicToolboxOpencartModuleInstallerClass() {
			global $admin_folder_name;
            $this->dir = dirname(dirname(__FILE__));
            $this->modDir = dirname(__FILE__) . '/module';
            $this->resDir = preg_replace('/^(.*?\/)[^\/]+\/[^\/]+$/is', '$1', $_SERVER['SCRIPT_NAME']) . $admin_folder_name .'/controller/module/magictoolbox';
        }

        function checkPlace() {
            $this->setStatus('check', 'place');
             if(!file_exists($this->dir . '/system/startup.php')) {
                $this->setError('Wrong location: please upload the files from the ZIP archive to the OpenCart store directory.');
                return false;
            }
            return true;
        }

        function checkPerm() {
            $this->setStatus('check', 'perm');
			global $admin_folder_name;

            $files = array(
                // directory
                '/'.$admin_folder_name.'/controller/module',
                '/'.$admin_folder_name.'/view/template/module',
                '/'.$admin_folder_name.'/view/image/',
                '/catalog/controller/product',
                '/catalog/controller/module',
                '/catalog/controller/common',
                // file
                '/catalog/controller/product/product.php',
                '/catalog/controller/product/category.php',
                '/catalog/controller/common/home.php',
                '/catalog/controller/common/header.php',
                '/catalog/controller/module/latest.php',
                '/catalog/controller/module/bestseller.php',
                '/catalog/controller/module/special.php',
                '/catalog/controller/module/featured.php'
                
            );

            /*vqmod fix start*/
            $files = $this->vqmod_fix($files);
            /*vqmod fix end*/

			$lang_dirs = array();
			$directories = glob($this->dir . '/'.$admin_folder_name.'/language/*' , GLOB_ONLYDIR);
			foreach ($directories as $ldir) {
				$ldir = preg_replace('/^.*\/([a-zA-Z\-\_]+$)/is','$1',$ldir);
				$files[] = '/'.$admin_folder_name.'/language/'.$ldir;
			}

            list($result, $wrang) = $this->checkFilesPerm($files);
            if(!$result) {
                $this->setError('This installer need to modify some OpenCart store files.');
                $this->setError('Please check write access for following files of your OpenCart store:');
                $this->setError($wrang, '&nbsp;&nbsp;&nbsp;-&nbsp;');
                return false;
            }
            return true;
        }

        function backupFiles() {
            $this->setStatus('backup', 'files');
            $backups = array(
                '/catalog/controller/product/product.php',
                '/catalog/controller/product/category.php',
                '/catalog/controller/common/home.php',
                '/catalog/controller/common/header.php',
                '/catalog/controller/module/latest.php',
                '/catalog/controller/module/bestseller.php',
                '/catalog/controller/module/special.php',
                '/catalog/controller/module/featured.php'
            );

            /*vqmod fix start*/
            $backups = $this->vqmod_fix($backups);
            /*vqmod fix end*/

            list($result, $wrang) = $this->createBackups($backups);
            if(!$result) {
                $this->setError('Can\'t create backups for following files:');
                $this->setError($wrang, '&nbsp;&nbsp;&nbsp;-&nbsp;');
                $this->setError('Please check write access');
                return false;
            }
            return true;
        }

        function restoreStep_backupFiles() {
            $backups = array(
                '/catalog/controller/product/product.php',
                '/catalog/controller/product/category.php',
                '/catalog/controller/common/home.php',
                '/catalog/controller/common/header.php',
                '/catalog/controller/module/latest.php',
                '/catalog/controller/module/bestseller.php',
                '/catalog/controller/module/special.php',
                '/catalog/controller/module/featured.php'
            );

            /*vqmod fix start*/
            $backups = $this->vqmod_fix($backups);
            /*vqmod fix end*/

            $this->removeBackups($backups);

            
        }

        function installFiles() {
            $this->setStatus('install', 'files');
			global $admin_folder_name;

            // copy folders
            $this->copyDir($this->modDir . '/admin', $this->dir . '/'.$admin_folder_name);
            
            // copy tabs.js file
            /*copy($this->modDir . '/tabs.js', $this->dir . '/js/tabs.js');
            @chmod($this->dir . '/js/tabs.js', 0755);*/

			$directories = glob($this->dir . '/'.$admin_folder_name.'/language/*' , GLOB_ONLYDIR);
			foreach ($directories as $ldir) {
				if (!file_exists($ldir.'/module/magicslideshow.php') && file_exists($this->dir . '/'.$admin_folder_name.'/language/english/module/magicslideshow.php')) {
					copy($this->dir . '/'.$admin_folder_name.'/language/english/module/magicslideshow.php',$ldir.'/module/magicslideshow.php');
				}
			}
                                

            //modify product.php 
            $c = file_get_contents($this->dir.'/catalog/controller/product/product.php');
       
            $pattern = 'class ControllerProductProduct extends Controller {';
            $replace = 'global $aFolder;
                        if (!defined(\'HTTP_ADMIN\')) define(\'HTTP_ADMIN\',\'admin\');
						$aFolder = preg_replace(\'/.*\/([^\/].*)\//is\',\'$1\',HTTP_ADMIN);
						if (!isset($GLOBALS[\'magictoolbox\'][\'magicslideshow\']) && !isset($GLOBALS[\'magicslideshow_module_loaded\'])) {
                            //include $aFolder.\'/controller/module/magictoolbox/module.php\'; 
                            include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?\'components/com_ayelshop/opencart/\':\'\').$aFolder.\'/controller/module/magictoolbox/module.php\';
                        };
                        class ControllerProductProduct extends Controller {';
            $c = str_replace($pattern, $replace, $c);

            $pattern = '\$this->response->setOutput\(\$this->render\(TRUE\), \$this->config->get\(\'config_compression\'\)\);';
            $replace = '$this->response->setOutput(magicslideshow($this->render(TRUE),$this,\'product\',$product_info), $this->config->get(\'config_compression\'));';
            $c = preg_replace('/'.$pattern.'/is', $replace, $c, 1); //only first needle replace

			/*FOR NEW OPEN CARTS*/
            $pattern = '\$this->response->setOutput\(\$this->render\(\)\);';
            $replace = '$this->response->setOutput(magicslideshow($this->render(TRUE),$this,\'product\',$product_info), $this->config->get(\'config_compression\'));';
            $c = preg_replace('/'.$pattern.'/is', $replace, $c, 1); //only first needle replace

            $pattern = '$results = $this->model_catalog_product->getProductImages($this->request->get[\'product_id\']);';
            $replace = '$results = $this->model_catalog_product->getProductImages($this->request->get[\'product_id\']); $product_info[\'images\'] = $results;';
            $c = str_replace($pattern, $replace, $c);
        
            file_put_contents($this->dir.'/catalog/controller/product/product.php', $c);
            /*vqmod fix start*/
            if ($vqName = $this->vqmod_fix('/catalog/controller/product/product.php')) {
                file_put_contents($this->dir.$vqName, $c);
            }
            /*vqmod fix end*/

            //modify category.php 
            $c = file_get_contents($this->dir . '/catalog/controller/product/category.php');
       
            $pattern = 'class ControllerProductCategory extends Controller {';
            $replace = 'global $aFolder;
                        if (!defined(\'HTTP_ADMIN\')) define(\'HTTP_ADMIN\',\'admin\');
						$aFolder = preg_replace(\'/.*\/([^\/].*)\//is\',\'$1\',HTTP_ADMIN);
						if (!isset($GLOBALS[\'magictoolbox\'][\'magicslideshow\']) && !isset($GLOBALS[\'magicslideshow_module_loaded\'])) {
                            //include $aFolder.\'/controller/module/magictoolbox/module.php\'; 
                            include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?\'components/com_ayelshop/opencart/\':\'\').$aFolder.\'/controller/module/magictoolbox/module.php\';
                        };
                        class ControllerProductCategory extends Controller {';
            $c = str_replace($pattern, $replace, $c);

            $pattern = '\$this->response->setOutput\(\$this->render\(TRUE\), \$this->config->get\(\'config_compression\'\)\);';
            $replace = '$this->response->setOutput(magicslideshow($this->render(TRUE),$this,\'category\', $results), $this->config->get(\'config_compression\'));';
            $c = preg_replace('/'.$pattern.'/is', $replace, $c, 1); //only first needle replace

			/*FOR NEW OPEN CARTS*/
            $pattern = '\$this->response->setOutput\(\$this->render\(\)\);';
            $replace = '$this->response->setOutput(magicslideshow($this->render(TRUE),$this,\'category\', $results), $this->config->get(\'config_compression\'));';
            $c = preg_replace('/'.$pattern.'/is', $replace, $c, 1); //only first needle replace



            file_put_contents($this->dir.'/catalog/controller/product/category.php', $c);
            /*vqmod fix start*/
            if ($vqName = $this->vqmod_fix('/catalog/controller/product/category.php')) {
                file_put_contents($this->dir.$vqName, $c);
            }
            /*vqmod fix end*/

            //modify catalog/controller/module/latest.php 
            $c = file_get_contents($this->dir . '/catalog/controller/module/latest.php');

            $pattern = '<?php';
            $replace = '<?php
						global $aFolder;
                        if (!defined(\'HTTP_ADMIN\')) define(\'HTTP_ADMIN\',\'admin\');
						$aFolder = preg_replace(\'/.*\/([^\/].*)\//is\',\'$1\',HTTP_ADMIN);
						if (!isset($GLOBALS[\'magictoolbox\'][\'magicslideshow\']) && !isset($GLOBALS[\'magicslideshow_module_loaded\'])) {
                            //include $aFolder.\'/controller/module/magictoolbox/module.php\'; 
                            include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?\'components/com_ayelshop/opencart/\':\'\').$aFolder.\'/controller/module/magictoolbox/module.php\';
                        };';
            $c = str_replace($pattern, $replace, $c);

            $pattern = '\$this->render\(\);';
			$replace = 'global $aFolder; include($aFolder.\'/controller/module/magictoolbox/boxes.inc\');';
            $c = preg_replace('/'.$pattern.'/is', $replace, $c, 1); //only first needle replace
            file_put_contents($this->dir.'/catalog/controller/module/latest.php', $c);
            /*vqmod fix start*/
            if ($vqName = $this->vqmod_fix('/catalog/controller/module/latest.php')) {
                file_put_contents($this->dir.$vqName, $c);
            }
            /*vqmod fix end*/

           //modify catalog/controller/module/special.php 
            $c = file_get_contents($this->dir . '/catalog/controller/module/special.php');

            $pattern = '<?php';
            $replace = '<?php
						global $aFolder;
                        if (!defined(\'HTTP_ADMIN\')) define(\'HTTP_ADMIN\',\'admin\');
						$aFolder = preg_replace(\'/.*\/([^\/].*)\//is\',\'$1\',HTTP_ADMIN);
						if (!isset($GLOBALS[\'magictoolbox\'][\'magicslideshow\']) && !isset($GLOBALS[\'magicslideshow_module_loaded\'])) {
                            //include $aFolder.\'/controller/module/magictoolbox/module.php\'; 
                            include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?\'components/com_ayelshop/opencart/\':\'\').$aFolder.\'/controller/module/magictoolbox/module.php\';
                        };';
            $c = str_replace($pattern, $replace, $c);

            $pattern = '\$this->render\(\);';
			$replace = 'global $aFolder; include($aFolder.\'/controller/module/magictoolbox/boxes.inc\');';
            $c = preg_replace('/'.$pattern.'/is', $replace, $c, 1); //only first needle replace
            file_put_contents($this->dir.'/catalog/controller/module/special.php', $c);
            /*vqmod fix start*/
            if ($vqName = $this->vqmod_fix('/catalog/controller/module/special.php')) {
                file_put_contents($this->dir.$vqName, $c);
            }
            /*vqmod fix end*/

            //modify catalog/controller/module/featured.php 
            $c = file_get_contents($this->dir . '/catalog/controller/module/featured.php');

            $pattern = '<?php';
            $replace = '<?php
						global $aFolder;
                        if (!defined(\'HTTP_ADMIN\')) define(\'HTTP_ADMIN\',\'admin\');
						$aFolder = preg_replace(\'/.*\/([^\/].*)\//is\',\'$1\',HTTP_ADMIN);
						if (!isset($GLOBALS[\'magictoolbox\'][\'magicslideshow\']) && !isset($GLOBALS[\'magicslideshow_module_loaded\'])) {
                            //include $aFolder.\'/controller/module/magictoolbox/module.php\'; 
                            include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?\'components/com_ayelshop/opencart/\':\'\').$aFolder.\'/controller/module/magictoolbox/module.php\';
                        };';
            $c = str_replace($pattern, $replace, $c);

            $pattern = '\$this->render\(\);';
			$replace = 'global $aFolder; include($aFolder.\'/controller/module/magictoolbox/boxes.inc\');';
            $c = preg_replace('/'.$pattern.'/is', $replace, $c, 1); //only first needle replace

			$pattern = '\$product_info = \$this\-\>model_catalog_product\-\>getProduct\(\$product_id\)\;';
			$replace = '$product_info = $this->model_catalog_product->getProduct($product_id); $product_infos[] = $product_info;';
			$c = preg_replace('/'.$pattern.'/is', $replace, $c);


            file_put_contents($this->dir.'/catalog/controller/module/featured.php', $c);
            /*vqmod fix start*/
            if ($vqName = $this->vqmod_fix('/catalog/controller/module/featured.php')) {
                file_put_contents($this->dir.$vqName, $c);
            }
            /*vqmod fix end*/

             //modify catalog/controller/module/bestseller.php 
            $c = file_get_contents($this->dir . '/catalog/controller/module/bestseller.php');

            $pattern = '<?php';
            $replace = '<?php
						global $aFolder;
                        if (!defined(\'HTTP_ADMIN\')) define(\'HTTP_ADMIN\',\'admin\');
						$aFolder = preg_replace(\'/.*\/([^\/].*)\//is\',\'$1\',HTTP_ADMIN);
						if (!isset($GLOBALS[\'magictoolbox\'][\'magicslideshow\']) && !isset($GLOBALS[\'magicslideshow_module_loaded\'])) {
                            //include $aFolder.\'/controller/module/magictoolbox/module.php\'; 
                            include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?\'components/com_ayelshop/opencart/\':\'\').$aFolder.\'/controller/module/magictoolbox/module.php\';
                        };';
            $c = str_replace($pattern, $replace, $c);

            $pattern = '\$this->render\(\);';
			$replace = 'global $aFolder; include($aFolder.\'/controller/module/magictoolbox/boxes.inc\');';
            $c = preg_replace('/'.$pattern.'/is', $replace, $c, 1); //only first needle replace
            file_put_contents($this->dir.'/catalog/controller/module/bestseller.php', $c);
            /*vqmod fix start*/
            if ($vqName = $this->vqmod_fix('/catalog/controller/module/bestseller.php')) {
                file_put_contents($this->dir.$vqName, $c);
            }
            /*vqmod fix end*/

            //modify catalog/controller/common/home.php
            $c = file_get_contents($this->dir . '/catalog/controller/common/home.php');

            $pattern = '\$this->response->setOutput\(\$this->render\(TRUE\), \$this->config->get\(\'config_compression\'\)\);';
            $replace = '$this->render();' . "\n\t\t" .
                        //'if (function_exists(\'set_headers\') && $this->config->get(\'magicslideshow_status\') != 0) {' . "\n\t\t\t" .
                        //    '$this->output = set_headers($this->output);' . "\n\t\t" .
                        //'}' . "\n\t\t" .
                        'if(version_compare(VERSION, \'1.4.9\', \'<\')) {' . "\n\t\t\t" .
                            '$this->output = magicslideshow($this->output,$this,\'latest_home_category\',$this->model_catalog_product->getLatestProducts(8));' . "\n\t\t" .
                        '}' . "\n\t\t" .
                        '$this->response->setOutput($this->output, $this->config->get(\'config_compression\'));';
            $c = preg_replace('/'.$pattern.'/is', $replace, $c, 1); //only first needle replace


            file_put_contents($this->dir.'/catalog/controller/common/home.php', $c);
            /*vqmod fix start*/
            if ($vqName = $this->vqmod_fix('/catalog/controller/common/home.php')) {
                file_put_contents($this->dir.$vqName, $c);
            }
            /*vqmod fix end*/

            //modify catalog/controller/common/header.php
            $c = file_get_contents($this->dir . '/catalog/controller/common/header.php');

            $pattern = '<?php';
            $replace = '<?php' . "\n\t" .
						'global $aFolder;' . "\n\t".
                        'if (!defined(\'HTTP_ADMIN\')) define(\'HTTP_ADMIN\',\'admin\');' . "\n\t".
						'$aFolder = preg_replace(\'/.*\/([^\/].*)\//is\',\'$1\',HTTP_ADMIN);' . "\n\t" .
                        'if (!isset($GLOBALS[\'magictoolbox\'][\'magicslideshow\']) && !isset($GLOBALS[\'magicslideshow_module_loaded\'])) {' . "\n\t\t" .
                            '//include $aFolder.\'/controller/module/magictoolbox/module.php\';' . "\n\t" .
                            'include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?\'components/com_ayelshop/opencart/\':\'\').$aFolder.\'/controller/module/magictoolbox/module.php\';'.
                        '}';
            $c = str_replace($pattern, $replace, $c);

            $pattern = '\$this->render\(\);';
            $replace = '$this->render();' . "\n\t\t" .
                        'if($this->config->get(\'magicslideshow_status\') != 0) {' . "\n\t\t\t" .
                            '$tool  = magicslideshow_load_core_class($this);' . "\n\t\t\t" .
                            'if(use_effect_on($tool)) {' . "\n\t\t\t\t" .
                                '$this->output = set_headers($this->output);' . "\n\t\t\t" .
                             '}' . "\n\t\t" .
                        '}';
            $c = preg_replace('/'.$pattern.'/is', $replace, $c, 1);

            file_put_contents($this->dir.'/catalog/controller/common/header.php', $c);
            /*vqmod fix start*/
            if ($vqName = $this->vqmod_fix('/catalog/controller/common/header.php')) {
                file_put_contents($this->dir.$vqName, $c);
            }
            /*vqmod fix end*/

            return true;
        }

        function restoreStep_installFiles() {
			global $admin_folder_name;
            $backups = array(
                '/catalog/controller/product/product.php',
                '/catalog/controller/product/category.php',
                '/catalog/controller/common/home.php',
                '/catalog/controller/common/header.php',
                '/catalog/controller/module/latest.php',
                '/catalog/controller/module/bestseller.php',
                '/catalog/controller/module/special.php',
                '/catalog/controller/module/featured.php'

            );
            /*vqmod fix start*/
            $backups = $this->vqmod_fix($backups);
            /*vqmod fix end*/
            $this->restoreFromBackups($backups);
			$this->removeDir($this->dir . '/'.$admin_folder_name.'/controller/module/magictoolbox');
            $this->removeDir($this->dir . '/'.$admin_folder_name.'/view/image/magictoolbox');


			$files_to_remove=array('/'.$admin_folder_name.'/controller/module/magicslideshow.php',
									'/'.$admin_folder_name.'/view/template/module/magicslideshow.tpl');

			$directories = glob($this->dir . '/'.$admin_folder_name.'/language/*' , GLOB_ONLYDIR);
			foreach ($directories as $ldir) {
				if (file_exists($ldir.'/module/magicslideshow.php')) {
					$files_to_remove[] =  str_replace($this->dir,'',$ldir.'/module/magicslideshow.php');
				}
			}

            $this->removeFiles($files_to_remove);

            return true;
        }

        function upgrade($files) {
			global $admin_folder_name;
            $path = $this->dir . '/'.$admin_folder_name.'/controller/module/magictoolbox/';
            foreach($files as $name => $file) {
                if(file_exists($path . $name)) {
                    unlink($path . $name);
                }
                file_put_contents($path . $name, $file);
                chmod($path . $name, 0755);
            }
            return true;
        }

        function vqmod_fix($input) {
            if (is_array($input)) {
                if (file_exists($this->dir.'/vqmod')) {
                    $files_array_add = array();
                    foreach ($input as $origFile) {
                        $vqName = '/vqmod/vqcache/vq2-'.str_replace('/','_',substr($origFile,1));
                        if (file_exists($this->dir.$vqName)) $files_array_add[] = $vqName;
                    }
                }
                if (is_array($files_array_add) && count($files_array_add) > 0) {
                    return array_merge($input,$files_array_add);
                } else {
                    return $input;
                }
            } else {
                $vqName = '/vqmod/vqcache/vq2-'.str_replace('/','_',substr($input,1));
                if (file_exists($this->dir.$vqName)) {
                    return $vqName; 
                } else {
                    return false;
                }
            }
        }

    }
