<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');
*/

$GLOBALS['magicslideshow_module_loaded'] = 'true'; // to fix boxes and pages conflict, I thunk we could find a better way in future



function magicslideshow($content, $currentController = false , $type = false, $info = false) {

    if ($currentController->config->get('magicslideshow_status') != 0) {
        $tool = & magicslideshow_load_core_class($currentController);

        //set_params_from_config($currentController->config);

        $enabled_on_this_page = false;

        unset($GLOBALS['magictoolbox']['items']);




        if ($tool->type == 'standard') { //do not apply MSS-like modules to category & product pages
            if ($type && $type == 'category' && !$tool->params->checkValue('use-effect-on-category-page', 'No')) {
                $enabled_on_this_page = true;

            }
            if ($type && $type == 'product' && !$tool->params->checkValue('use-effect-on-product-page', 'No')) {
                $enabled_on_this_page = true;


            }
        }

        if ($tool->type == 'circle') { //Apply 360 only to Products Page 
            if ($type && $type == 'product') {
                    $enabled_on_this_page = true;
            }

		} else {

			if ($type && ($type == 'latest_home_category' || $type == 'latest_home' || $type == 'latest_right' || $type == 'latest_left' || $type == 'latest_content_top' || $type == 'latest_content_bottom' || $type == 'latest_column_left' || $type == 'latest_column_right') && !$tool->params->checkValue('use-effect-on-latest-box', 'No')) {
				$enabled_on_this_page = true;

			}
			if ($type && ($type == 'featured_home' || $type == 'featured_right' || $type == 'featured_left' || $type == 'featured_left' || $type == 'featured_content_top' || $type == 'featured_content_bottom' || $type == 'featured_column_left' || $type == 'featured_column_right') && !$tool->params->checkValue('use-effect-on-featured-box', 'No') ) {
				$enabled_on_this_page = true;

			}
			if ($type && ($type == 'special_home' || $type == 'special_right' || $type == 'special_left' || $type == 'special_content_top' || $type == 'special_content_bottom' || $type == 'special_column_left' || $type == 'special_column_right') && !$tool->params->checkValue('use-effect-on-special-box', 'No')) {
				$enabled_on_this_page = true;

			}
			if ($type && ($type == 'bestseller_home' || $type == 'bestseller_right' || $type == 'bestseller_left' || $type == 'bestseller_content_top' || $type == 'bestseller_content_bottom' || $type == 'bestseller_column_left' || $type == 'bestseller_column_right') && !$tool->params->checkValue('use-effect-on-bestsellers-box', 'No')) {
				$enabled_on_this_page = true;

			}

		}


        //if ($type == 'product' || $type == 'category')  { //hack! TODO: load headers only if we need them
        //    $content = set_headers($content);
        //}

        if ($enabled_on_this_page) {

            if ($type) $GLOBALS['magictoolbox']['page_type'] = $type;
            if ($info ) $GLOBALS['magictoolbox']['prods_info'] = $info;


            $content = set_headers($content);
            $content = parse_contents($content,$currentController);


            if ($type == 'product' && $tool->type == 'standard' && isset($GLOBALS['magictoolbox']['MagicSlideshow']['main'])) {
                // template helper class
                require_once(dirname(__FILE__) . '/magictoolbox.templatehelper.class.php');
                MagicToolboxTemplateHelperClass::setPath(dirname(__FILE__).DIRECTORY_SEPARATOR.'templates');
                MagicToolboxTemplateHelperClass::setOptions($tool->params);
                $html = MagicToolboxTemplateHelperClass::render(array(
                    'main' => $GLOBALS['magictoolbox']['MagicSlideshow']['main'],
                    'thumbs' => (count($GLOBALS['magictoolbox']['MagicSlideshow']['selectors']) > 1) ? $GLOBALS['magictoolbox']['MagicSlideshow']['selectors'] : array(),
                    'pid' => $GLOBALS['magictoolbox']['prods_info']['product_id'],
                ));

                $content = str_replace('MAGICTOOLBOX_PLACEHOLDER', $html, $content);
            }



        }
    }

    return $content;
}

function set_headers ($content, $headers = false) {

	if(defined('HTTP_ADMIN')) {
		$aFolder = preg_replace('/.*\/([^\/].*)\//is','$1',HTTP_ADMIN);
	} else {
		$aFolder = 'admin';
	}

	/*fix css with admin folder name*/
    $cssPath = dirname(__FILE__).'/magicslideshow.css';
    if (filesize($cssPath) > 0) copy($cssPath,$cssPath.'~backup');
	$css = file_get_contents($cssPath);
	$css = preg_replace ('/(\/[^\/]*?\/controller)/is','/'.$aFolder.'/controller',$css);
    if (is_writable($cssPath) && strlen(trim($css)) > 0) {
        clearstatcache();
        if (!file_put_contents($cssPath,$css) || (filesize($cssPath) == 0)) {
            unlink($cssPath);
            rename($cssPath.'~backup',$cssPath);
        }
    }

	/*end fix*/

    $plugin = $GLOBALS["magictoolbox"]["magicslideshow"];

    if (!$headers) {


        if (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__) || strpos($content,'</head>')) { //TODO enhance this part
            $ayprefix = (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?'components/com_ayelshop/opencart/':'');
            $headers = $plugin->headers($ayprefix.$aFolder.'/controller/module/magictoolbox',$ayprefix.$aFolder.'/controller/module/magictoolbox');
        }
        //$headers = $plugin->headers($aFolder.'/controller/module/magictoolbox',$aFolder.'/controller/module/magictoolbox');




    }


    if (!$plugin->params->checkValue('use-effect-on-category', 'No')) {//fix for category view switch
        $headers .= '<script type="text/javascript">
                    $mjs(document).je1(\'domready\', function() {
                      if (typeof display !== \'undefined\') {
                        var olddisplay = display;
                        window.display = function (view) {
                          MagicSlideshow.stop();
                          olddisplay(view);
                          MagicSlideshow.start();
                        }
                      }
                    });
                   </script>';
    }


    if ($headers && $content && !isset($GLOBALS['magicslideshow_headers_set'])) {

        if (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)) {
            $content = $headers.$content;
            $GLOBALS['magicslideshow_headers_set'] = true;
        } else {
            $content = preg_replace('/\<\/head\>/is',"\n".$headers."\n</head>",$content,-1,$matched);
        }
        //$content = preg_replace('/\<\/head\>/is',"\n".$headers."\n</head>",$content,-1,$matched);

        if ($matched > 0) $GLOBALS['magicslideshow_headers_set'] = true;
    }
    return $content;
}

function &magicslideshow_load_core_class($currentController = false) {
    if(!isset($GLOBALS["magictoolbox"])) $GLOBALS["magictoolbox"] = array();
    if(!isset($GLOBALS["magictoolbox"]["magicslideshow"])) {
        /* load core class */
        require_once(dirname(__FILE__) . '/magicslideshow.module.core.class.php');
        $tool = new MagicSlideshowModuleCoreClass();
        /* add category for core params */
        $params = $tool->params->getArray();
        foreach($params as $k => $v) {
            $v['category'] = array(
                "name" => 'General options',
                "id" => 'general-options'
            );
            $params[$k] = $v;
        }
        $tool->params->appendArray($params);



        $GLOBALS["magictoolbox"]["magicslideshow"] = & $tool;
    }
    if($currentController) {

        $GLOBALS['magictoolbox']['currentController'] = $currentController; //SEO url fixe

        $query = $currentController->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = 'magicslideshow'");
        foreach($query->rows as $param) {
            $GLOBALS["magictoolbox"]["magicslideshow"]->params->set($param['key'],$param['value']);
        }

    }
    return $GLOBALS["magictoolbox"]["magicslideshow"];
}

function parse_contents($content,$currentController) {
    $plugin = $GLOBALS['magictoolbox']['magicslideshow'];
    $type = $GLOBALS['magictoolbox']['page_type'];

    //some bugs fix
    $content = str_replace("<!--code start-->",'',$content);
    $content = str_replace("<!--code end-->",'',$content);

    if ($type == 'product') {
        $content = fixProductCss($content); //fix most css issues on product page
        $enabled = true;
        if ($plugin->type == 'circle') {
            $enabled = $plugin->enabled($GLOBALS['magictoolbox']['prods_info']['images'],$GLOBALS['magictoolbox']['prods_info']['product_id']);
        }

        if ($enabled) {
            $pattern = '(?:<a([^>]*)>)[^<]*<img([^>]*)(?:>)(?:[^<]*<\/img>)?(.*?)[^<]*?<\/a>';
            $content = preg_replace_callback("/{$pattern}/is",'magicslideshow_callback',$content);
            //add main image to additional
            if (!isset($GLOBALS['magictoolbox'][strtoupper('magicslideshow').'_MAIN_IMAGE_AFFECTED'])) return $content;
            $thumb = $GLOBALS['magictoolbox'][strtoupper('magicslideshow').'_MAIN_IMAGE_AFFECTED'];

            if ($plugin->type == 'circle') {
                $content = preg_replace('/<a[^>]*?\#tab_image.*?>.*?<\/a>/is','',$content); // CUT SELECTORS TAB
                $content = preg_replace('/<a[^>]*?\#product_gallery.*?>.*?\/a>/is','',$content); // CUT SELECTORS TAB (shoppica)
                $content = preg_replace('/<div[^>]*?id=\"tab_image\"[^>*?]class=\"tab_page\".*?>.*?<div id="tab_related"/is','<div id="tab_related"',$content); // CUT SELECTORS DIV
                $content = preg_replace('/<div[^>]*?class=\"image-additional\"[^>]*?>.*?<\/div>/is','',$content); // CUT SELECTORS DIV

                $content = str_replace ('magicslideshow_MAIN_IMAGE',$plugin->template($GLOBALS['magictoolbox']['items']),$content); //REPLACE MAIN IMAGE WITH EFFECT
            }

            $content = str_replace('<div class="image-additional">','<div class="image-additional">'.$thumb.' ',$content);
            $content = preg_replace('/<a[^>]*?\#product_gallery.*?>.*?\/a>/is','',$content); // CUT SELECTORS TAB (shoppica)
            $content = preg_replace('/<span[^>]*?>[^<]*?'.$currentController->language->get('text_enlarge').'[^<]*?<\/span>/is','',$content); //REMOVE DEFAULT "Click to Enlarge"
        }


    } else if ($type == 'category' || strpos($type,'content_top') || strpos($type,'content_bottom') ||
			  ($type == 'latest_home_category' && $GLOBALS['magictoolbox']['page_type'] == 'latest_home') || 
		      ($type == 'featured_home_category' && $GLOBALS['magictoolbox']['page_type'] == 'featured_home')) {
        //if($type == 'latest_home_category') $GLOBALS['magictoolbox']['page_type'] = 'latest_home';
        preg_match_all('/<table[^>]class=[\"\']list[\'\"]>.*?<\/table>/is',$content,$table_contents);


		if (empty($table_contents[0]) && count($table_contents) < 2) { //FOR NEW OPENCART
			preg_match_all('/<div class="product-list">.*?<div class="pagination">/is',$content,$table_contents);
			if (empty($table_contents[0]) && count($table_contents) < 2) { //FOR OPENCART 1.5.x
				preg_match_all('/<div class="box-product">.*?<\/div>[^<]*<\/div>[^<]*<\/div>/is',$content,$table_contents);
			}
			$content = str_replace('</head>','
						<style type="text/css">
						.product-list > div {
							overflow: visible !important;
						}
						.product-list .description {
							clear:right;
						}</style></head>',$content);
		}


        $pattern = '(?:<a([^>]*)>)[^<]*<img([^>]*)(?:>)(?:[^<]*<\/img>)?(.*?)[^<]*?<\/a>';
        if (isset($table_contents[0]) && !is_array($table_contents[0])) {
            $result = preg_replace_callback("/{$pattern}/is",'magicslideshow_callback_category',$table_contents[0]);
        } else if (isset($table_contents[0][0]) && !is_array($table_contents[0][0])) {
            $result = preg_replace_callback("/{$pattern}/is",'magicslideshow_callback_category',$table_contents[0][0]);
        }

		if ($plugin->type == 'standard') {
            if (isset($table_contents[0]) && !is_array($table_contents[0])) {
                $content = str_replace($table_contents[0],$result,$content);
            } else if (isset($table_contents[0][0]) && !is_array($table_contents[0][0])) {
                $content = str_replace($table_contents[0][0],$result,$content);
            }
		} else if (isset($GLOBALS['magictoolbox']['items']) && count($GLOBALS['magictoolbox']['items']) >= $plugin->params->getValue('items')) {
			$options['id'] = $type;
			$options['title'] = 'Right';
			
            foreach($GLOBALS['magictoolbox']['items'] as $k => $v) {
                unset($GLOBALS['magictoolbox']['items'][$k]['description']);
            }
			
			$direction_current = $plugin->params->getValue('direction');
			$plugin->general->params['direction'] = $plugin->params->params['direction'];
			$plugin->params->set('direction','right');

			
			$plugin->general->params['width'] = $plugin->params->params['width'];
			$plugin->params->set('width',$plugin->params->params['home-thumb-max-width']['value']);

			$content = str_replace($table_contents[0],$plugin->template($GLOBALS['magictoolbox']['items'],$options),$content);
		}



    } else if ($type) {
        $pattern = '(?:<a([^>]*)>)[^<]*<img([^>]*)(?:>)(?:[^<]*<\/img>)?(.*?)[^<]*?<\/a>';
        $result = preg_replace_callback("/{$pattern}/is",'magicslideshow_callback_category',$content);

        if ($plugin->type == 'standard') {
            $content = str_replace($content,$result,$content);
         } else if (isset($GLOBALS['magictoolbox']['items'])

            ) { //SLIDESHOW
            if (VERSION >= 1.5) {
				$pattern = '(^.*?<div class="box-product">)(.*)';
			} else {
				$pattern = '(^.*?<div[^>]*?\"middle\">)(.*)?(<div[^>]*?\"bottom">.*)';
			}

            if (!strpos($type,'_home') && !strpos($type,'content_')) {

                $thumbs_current = $plugin->params->getValue('thumbnails');
                $plugin->params->set('thumbnails','off');

                $arrows_current = $plugin->params->getValue('arrows');
                $plugin->params->set('arrows','No');

                $direction_current = $plugin->params->getValue('direction');
                $plugin->general->params['direction'] = $plugin->params->params['direction'];
                $plugin->params->set('direction','bottom');

            } /*else if (strpos($type,'content_')) {

				$direction_current = $plugin->params->getValue('direction');
                $plugin->params->set('direction','right');
				$options['direction'] = 'right';

			}*/

            $options['id'] = $type;
            foreach($GLOBALS['magictoolbox']['items'] as $k => $v) {
                unset($GLOBALS['magictoolbox']['items'][$k]['description']);
            }
			if (VERSION >= 1.5) {
				$content = preg_replace("/{$pattern}/is",'$1'.$plugin->template($GLOBALS['magictoolbox']['items'],$options).'</div></div></div>',$content);
			} else {
				$content = preg_replace("/{$pattern}/is",'$1'.$plugin->template($GLOBALS['magictoolbox']['items'],$options).'</div>$3',$content);
			}
        }
    }
if (isset($thumbs_current)) $plugin->params->set('thumbnails',$thumbs_current);
if (isset($arrows_current)) $plugin->params->set('arrows',$arrows_current);
if (isset($direction_current)) $plugin->params->set('direction',$direction_current);
return $content;
}



function magicslideshow_callback_category ($matches) {

    if (preg_match("/data\/Stick_Gallery/ims",$matches[0])) return $matches[0];//Product Label module support

    $plugin = $GLOBALS["magictoolbox"]["magicslideshow"];
    $plugin_enabled = true;
    $result = $matches[0];
    if ($plugin_enabled) {
        $show_message_current = $plugin->params->getValue('show-message');
        $plugin->params->set('show-message','No');
        $caption_source_current = $plugin->params->getValue('caption-source');
        $plugin->params->set('caption-source','Title');
        $shop_dir = str_replace('system/','',DIR_SYSTEM);
        $image_dir = str_replace ($shop_dir,'',DIR_IMAGE);
        $type = $GLOBALS['magictoolbox']['page_type'];
        $link = preg_replace("/^.*?href\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[1]);

        $id = preg_replace('/^.*?id=(\d+).*/is','$1',$link);

        if (!strpos($link,'product_id')) { //SEO links fix
            $furl = substr($link, strrpos($link, '/') + 1);
            $currentController = $GLOBALS['magictoolbox']['currentController'];
            $query = $currentController->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '".$furl."'");
            $query = $query->rows[0]['query'];
            $id = preg_replace('/^.*?id=(\d+).*/is','$1',$query);
        }

        if (!is_numeric($id)) return $matches[0];
        $pid = $id;
        $p_info = getProductParams($id,$GLOBALS['magictoolbox']['prods_info']);
        if ($p_info['image'] == '') return $matches[0];
        $id = $id.'_'.$type;

        if ($plugin->params->checkValue('link-to-product-page','No')) $link='';
        $title = $p_info['name'];
        $title = htmlspecialchars(htmlspecialchars_decode($title, ENT_QUOTES));
        $description = $p_info['description'];
        $description = htmlspecialchars(htmlspecialchars_decode($description, ENT_QUOTES));

        $group = $type;

        $original = $image_dir.$p_info['image'];
        $img = getThumb($original,'original',$pid);

        if ($type != 'category') {
            $position = preg_replace('/.*?_(.*)/is','$1',$type);
        } else {
            $position = $type;
        }

        if ($plugin->type == 'standard') {
            $position = str_replace('column_','',$position);
			$cat_array=array('home','content_bottom','content_top');
            if (in_array($position,$cat_array)) $position = 'category';
            $thumb = getThumb($original,$position.'-thumb',$pid);
            $result = $plugin->template(compact('img','thumb','id','title','description','link','group'));
        } else {
			$position = str_replace('column_','',$position);
			if ($position == 'content_bottom' || $position == 'content_top') $position = 'category';
            $img = getThumb($original,$position.'-thumb',$pid);
            $thumb = getThumb($original,'home-selector-thumb',$pid);
            $GLOBALS['magictoolbox']['items'][] = compact('img','thumb','id','title','description','link');
        }
    }

    $plugin->params->set('show-message',$show_message_current);
    $plugin->params->set('caption-source',$caption_source_current);
    return $result;
}

function magicslideshow_callback ($matches) {

    if (preg_match("/data\/Stick_Gallery/ims",$matches[0])) return $matches[0];//Product Label module support

    $plugin = $GLOBALS["magictoolbox"]["magicslideshow"];
    $plugin_enabled = true;
    $result = $matches[0];
    if(!preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*thickbox(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) && 
       !preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*fancybox(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*lightbox(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*cloud\-zoom(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/rel\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*colorbox(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/rel\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*prettyPhoto\[gallery\](?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0])) {
        $plugin_enabled = false;
    }
    if ($plugin_enabled) {
        $shop_dir = str_replace('system/','',DIR_SYSTEM);
        $image_dir = str_replace ($shop_dir,'',DIR_IMAGE);

        $title = $GLOBALS['magictoolbox']['prods_info']['name'];
        $title = htmlspecialchars(htmlspecialchars_decode($title, ENT_QUOTES));
        $description = $GLOBALS['magictoolbox']['prods_info']['description'];
        $description = htmlspecialchars(htmlspecialchars_decode($description, ENT_QUOTES));

        $img = preg_replace("/^.*?href\s*=\s*[\"\'].*\/(.*?)-\d+x\d+.*[\"\'].*$/is","$1",$matches[1]);
        $img = preg_replace('/([\(\)])/is','\\\$1',$img); // all brackets are escaped now =)

        $original_image = false;
        if (isset($GLOBALS['magictoolbox'][strtoupper('magicslideshow').'_MAIN_IMAGE_AFFECTED'])) {
            foreach ($GLOBALS['magictoolbox']['prods_info']['images'] as $image) {
            if (preg_match('/.*?'.$img.'\.(png|jpg|jpeg|gif)/is',$image['image'])) {
                $original_image = $image['image'];
            }
            }
        } else {
            $original_image = $GLOBALS['magictoolbox']['prods_info']['image'];
        }
        if (!$original_image) return $matches[0];

        $id = $GLOBALS['magictoolbox']['prods_info']['product_id'];

        $original_image = $image_dir.$original_image;
        $img = getThumb($original_image,'original',$id);
        $selector = getThumb($original_image,'selector',$id);
        $medium = getThumb($original_image,null,$id);
        $thumb = $selector;

        if ($plugin->type == 'standard') {

            if (!isset($GLOBALS['magictoolbox'][strtoupper('magicslideshow').'_MAIN_IMAGE_AFFECTED'])) {
                $additional_result = $plugin->subTemplate(compact('alt','img','medium','thumb','id'));
                $GLOBALS['magictoolbox'][strtoupper('magicslideshow').'_MAIN_IMAGE_AFFECTED'] = '';// $additional_result;

                $thumb = getThumb($original_image,null,$id);
                $result = $plugin->template(compact('img','thumb','id','title','description'));

                $GLOBALS['magictoolbox']['MagicSlideshow']['selectors'][] = $additional_result;
                $GLOBALS['magictoolbox']['MagicSlideshow']['main'] = $result;

                return 'MAGICTOOLBOX_PLACEHOLDER';

            } else {
                $result = $plugin->subTemplate(compact('alt','img','medium','thumb','id'));

                $GLOBALS['magictoolbox']['MagicSlideshow']['selectors'][] = $result;
                return '';
            }
        } else if ($plugin->type == 'circle') {
            if (!isset ($GLOBALS['magictoolbox'][strtoupper('magicslideshow').'_MAIN_IMAGE_AFFECTED'])) {
                $result = 'magicslideshow_MAIN_IMAGE';
                $GLOBALS['magictoolbox'][strtoupper('magicslideshow').'_MAIN_IMAGE_AFFECTED'] = $matches[0];
            } else {
                $result = $matches[0];
            }
            $GLOBALS['magictoolbox']['items'][] = array('medium' => $medium, 'img' => $thumb);
        }
    }
return $result;
}

function getProductParams ($id, $params = false) {
    if (!$params) $params = $GLOBALS['magictoolbox']['prods_info'];
    foreach ($params as $key=>$product_array) {
        if ($product_array['product_id'] == $id) {
            return $product_array;
        }
    }
}

function getThumb($src, $size = null, $pid = null) {
    if($size === null) $size = 'thumb';
    require_once(dirname(__FILE__) . '/magictoolbox.imagehelper.class.php');
    $url = str_replace('image/','',HTTP_IMAGE);
    $shop_dir = str_replace('system/','',DIR_SYSTEM);
    $image_dir = str_replace ($shop_dir,'',DIR_IMAGE);

    $imagehelper = new MagicToolboxImageHelperClass($shop_dir, '/'.$image_dir.'magictoolbox_cache', $GLOBALS["magictoolbox"]["magicslideshow"]->params, null, $url);
    return $imagehelper->create('/' . $src, $size, $pid);
}

function set_params_from_config ($config = false) {
    if ($config) {
        $plugin = $GLOBALS["magictoolbox"]["magicslideshow"];

        foreach ($plugin->params->getNames() as $name) {
            if ($config->get($name)) {
                $plugin->params->set($name,$config->get($name));
            }
        }
        foreach ($plugin->params->getArray() as $param) {//fill empty values with defaults (fixes 'NOTICES bug')
            if (!isset($param['value'])) {
                $plugin->params->set($param['id'],$plugin->params->getValue($param['id']));
            }
        }

        $plugin->general->appendArray($plugin->params->getArray());
    }
}

function use_effect_on(&$tool) {
    return !$tool->params->checkValue('use-effect-on-product-page','No') ||
           !$tool->params->checkValue('use-effect-on-category-page','No') ||
           !$tool->params->checkValue('use-effect-on-latest-box','No') ||
           !$tool->params->checkValue('use-effect-on-featured-box','No') ||
           !$tool->params->checkValue('use-effect-on-special-box','No') ||
           !$tool->params->checkValue('use-effect-on-bestsellers-box','No');
}

function fixProductCss ($content) {
    $columns = 0;
    $columnLeft = $columnRight = false;
    if (true == strpos($content,'<div id="column-left">')) {
        $columns++;
        $columnLeft = true;
    }
    if (true == strpos($content,'<div id="column-right">')) {
        $columns++;
        $columnRight = true;
    }
    $cssWidth = array('950','770','585');
    $css = '.product-info { overflow:visible !important; }
            #tabs { clear:both; }
            ';
    if ($columns != 0) $css .= '#content { float:left; width:'.$cssWidth[$columns].'px; margin-left:15px !important;  }';
    if ($columns == 2) $css .= '#content { margin-right:15px !important; }';
    if ($columns == 1 && $columnRight) $css .= '#content { margin-right:0px !important; }';
    $content = str_replace('</head>',"\n<style type=\"text/css\">".$css."</style>\n</head>",$content);
    return $content;
}

?>
