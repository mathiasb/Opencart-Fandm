<?php

if(!defined('MagicSlideshowModuleCoreClassLoaded')) {

    define('MagicSlideshowModuleCoreClassLoaded', true);

    require_once(dirname(__FILE__) . '/magictoolbox.params.class.php');

    class MagicSlideshowModuleCoreClass {
        var $params;
        var $general;//initial parameters

        // set module type
        var $type = 'category';

        function MagicSlideshowModuleCoreClass() {
            // init params
            $this->params = new MagicToolboxParamsClass();
            $this->general = new MagicToolboxParamsClass();
            // load default params
            $this->_paramDefaults();
        }

        function headers($jsPath = '', $cssPath = null, $notCheck = false) {

            //to prevent multiple displaying of headers
            if(!defined('MagicSlideshowModuleHeaders')) {
                define('MagicSlideshowModuleHeaders', true);
            } else {
                return '';
            }
            if($cssPath == null) {
                $cssPath = $jsPath;
            }
            $headers = array();
            // add module version
            $headers[] = '<!-- Magic Slideshow OpenCart module version v2.8.36 [v1.1.20:v1.1.22] -->';
            // add style link
            $headers[] = '<link type="text/css" href="' . $cssPath . '/magicslideshow.css" rel="stylesheet" media="screen" />';
            // add script link
            $headers[] = '<script type="text/javascript" src="' . $jsPath . '/magicslideshow.js"></script>';
            // add options
            $headers[] = '<script type="text/javascript">MagicSlideshow.options = {' . implode(',', $this->options()) . '}</script>';
            return implode("\r\n", $headers);

        }

        function options() {

            // check params width 'auto' value
            if($this->params->checkValue('width', 0)) {
                $this->params->set('width', 'auto');
            }
            if($this->params->checkValue('height', 0)) {
                $this->params->set('height', 'auto');
            }
            if($this->params->checkValue('container-size', 0)) {
                $this->params->set('container-size', 'auto');
            }

            // load module options
            $options = array(
                'speed: ' . $this->params->getValue('speed'),
                'loop: \'' . $this->params->getValue('loop') . '\'',
                'loopType: \'' . $this->params->getValue('loop-type') . '\'',
                'thumbnails: \'' . $this->params->getValue('thumbnails') . '\'',
                'width: \'' . $this->params->getValue('width') . '\'',
                'height: \'' . $this->params->getValue('height') . '\'',
                'arrows: \'' . $this->params->getValue('arrows') . '\'',
                'thumbnailOpacity: ' . $this->params->getValue('thumbnail-opacity'),
                'direction: \'' . $this->params->getValue('direction') . '\'',
                'effect: \'' . $this->params->getValue('effect') . '\'',
                'effectNext: \'' . $this->params->getValue('effect-next') . '\'',
                'effectJump: \'' . $this->params->getValue('effect-jump') . '\'',
                'effectDuration: ' . $this->params->getValue('effect-duration'),
                'text: \'' . $this->params->getValue('text') . '\'',
                'textEffect: \'' . $this->params->getValue('text-effect') . '\'',
                'textDelay: ' . $this->params->getValue('text-delay'),
                'textPosition: \'' . $this->params->getValue('text-position') . '\'',
                'textOpacity: ' . $this->params->getValue('text-opacity'),
                'containerSize: \'' . $this->params->getValue('container-size') . '\'',
                'containerPosition: \'' . $this->params->getValue('container-position') . '\'',
                'containerOpacity: ' . $this->params->getValue('container-opacity'),
                'containerSpeed: ' . $this->params->getValue('container-speed'),
                'containerPadding: ' . $this->params->getValue('container-padding'),
                'preserve: \'' . $this->params->getValue('preserve') . '\'',
                'loadingText: \'' . $this->params->getValue('loading-text') . '\'',
                'start: ' . $this->params->getValue('start'),
                'zIndex: ' . $this->params->getValue('z-index'),
                'linksWindow: \'' . $this->params->getValue('links-window') . '\'',
                'pause: \'' . $this->params->getValue('pause') . '\'',
            );

            return $options;
        }

        function enabled($data, $id) {
            return true;
        }

        function template($data, $params = array()) {

            $html = array();

            extract($params);

            // check for width/height
            if(!isset($width) || empty($width)) {
                $width = "";
            } else {
                $width = " width=\"{$width}\"";
            }
            if(!isset($height) || empty($height)) {
                $height = "";
            } else {
                $height = " height=\"{$height}\"";
            }

            // check ID
            if(!isset($id) || empty($id)) {
                $id = '';
            } else {
                // add personal options
                $html[] = $this->getPersonalOptions($id);
                $id = ' id="' . addslashes($id) . '"';
            }

	    $notShowText = $this->params->checkValue('show-text', 'No');

            // add div with tool className
            $html[] = '<div' . $id . ' class="MagicSlideshow"' . $width . $height . '>';

            // add items
            foreach($data as $item) {
                extract($item);

                if(isset($medium) && $this->type == 'circle') {
                    $img = $medium;
                }

                // check item link
                if(!isset($link) || empty($link)) {
                    $link = '';
                } else {
                    // check target
                    if(isset($target) && !empty($target)) {
                        $target = ' target="' . $target . '"';
                    } else {
                        $target = '';
                    }
                    $link = $target . ' href="' . addslashes($link) . '"';
                }

                // check item alt tag
                if(!isset($alt) || empty($alt) || $notShowText) {
                    $alt = '';
                } else {
                    $alt = htmlspecialchars(htmlspecialchars_decode($alt, ENT_QUOTES));
                }

                // check big image
                if(!isset($img) || empty($img)) {
                    //return false;
                    // check thumbnail
                    if(!isset($thumb) || empty($thumb)) {
                        $thumb = '';
                    }
                    $img = '';
                } else {
                    // check thumbnail
                    if(!isset($thumb) || empty($thumb)) {
                        $thumb = $img;
                    }
                    $img = ' rel="' . $img . '"';
                }

                // check title
                if(!isset($title) || empty($title)) {
                    $title = '';
                } else {
                    $title = htmlspecialchars(htmlspecialchars_decode($title, ENT_QUOTES));
                    if(!$notShowText && empty($alt)) {
                        $alt = $title;
                    }
                    $title = " title=\"{$title}\"";
                }

                // check description
                if(!isset($description) || empty($description)) {
                    $description = '';
                } else {
                    $description = preg_replace("/<(\/?)a([^>]*)>/is", "[$1a$2]", $description);
                    $description = "<span>{$description}</span>";
                }

                // check item width
                if(!isset($width) || empty($width)) {
                    $width = "";
                } else {
                    $width = " width=\"{$width}\"";
                }

                // check item height
                if(!isset($height) || empty($height)) {
                    $height = "";
                } else {
                    $height = " height=\"{$height}\"";
                }

                // add item
                $html[] = "<a{$link}{$title}{$img}><img{$width}{$height} src=\"{$thumb}\" alt=\"{$alt}\" />$description</a>";
                unset ($alt); //temp FIX
            }

            // close core div
            $html[] = '</div>';

            // check and add message
            if($this->params->checkValue('show-message', 'Yes')) {
                $html[] = '<div class="MagicToolboxMessage">' . $this->params->getValue('message') . '</div>';
            } else $html[] = '';

            // create HTML string
            $html = implode('', $html);

            // return result
            return $html;
        }

        function subTemplate() {
            $args = func_get_args();
            call_user_func_array(array($this, 'template'), $args);
        }

        function addonsTemplate() {
            return '';
        }

        function getPersonalOptions($id) {
            if(defined('MagicToolboxOptionsLoaded')) {
                return '<script type="text/javascript">MagicSlideshow.extraOptions.' . $id . ' = {' . $this->params->serialize(null, true) . '};</script>';
            }
            $options = array();
            if(count($this->general->params)) {
                foreach($this->general->params as $name => $param) {
                    if($this->params->checkValue($name, $this->general->getValue($name))) continue;
                    switch($name) {
                        case 'speed':
                            $options[] = 'speed: ' . $this->params->getValue('speed');
                            break;
                        case 'loop':
                            $options[] = 'loop: \'' . $this->params->getValue('loop') . '\'';
                            break;
                        case 'loop-type':
                            $options[] = 'loopType: \'' . $this->params->getValue('loop-type') . '\'';
                            break;
                        case 'thumbnails':
                            $options[] = 'thumbnails: \'' . $this->params->getValue('thumbnails') . '\'';
                            break;
                        case 'width':
                            if($this->params->checkValue('width', 0)) {
                                $options[] = 'width: \'auto\'';
                            } else {
                                $options[] = 'width: \'' . $this->params->getValue('width') . '\'';
                            }
                            break;
                        case 'height':
                            if($this->params->checkValue('height', 0)) {
                                $options[] = 'height: \'auto\'';
                            } else {
                                $options[] = 'height: \'' . $this->params->getValue('height') . '\'';
                            }
                            break;
                        case 'arrows':
                            $options[] = 'arrows: \'' . $this->params->getValue('arrows') . '\'';
                            break;
                        case 'thumbnail-opacity':
                            $options[] = 'thumbnailOpacity: ' . $this->params->getValue('thumbnail-opacity');
                            break;
                        case 'direction':
                            $options[] = 'direction: \'' . $this->params->getValue('direction') . '\'';
                            break;
                        case 'effect':
                            $options[] = 'effect: \'' . $this->params->getValue('effect') . '\'';
                            break;
                        case 'effect-next':
                            $options[] = 'effectNext: \'' . $this->params->getValue('effect-next') . '\'';
                            break;
                        case 'effect-jump':
                            $options[] = 'effectJump: \'' . $this->params->getValue('effect-jump') . '\'';
                            break;
                        case 'effect-duration':
                            $options[] = 'effectDuration: ' . $this->params->getValue('effect-duration');
                            break;
                        case 'text':
                            $options[] = 'text: \'' . $this->params->getValue('text') . '\'';
                            break;
                        case 'text-effect':
                            $options[] = 'textEffect: \'' . $this->params->getValue('text-effect') . '\'';
                            break;
                        case 'text-delay':
                            $options[] = 'textDelay: ' . $this->params->getValue('text-delay');
                            break;
                        case 'text-position':
                            $options[] = 'textPosition: \'' . $this->params->getValue('text-position') . '\'';
                            break;
                        case 'text-opacity':
                            $options[] = 'textOpacity: ' . $this->params->getValue('text-opacity');
                            break;
                        case 'container-size':
                            if($this->params->checkValue('container-size', 0)) {
                                $options[] = 'containerSize: \'auto\'';
                            } else {
                                $options[] = 'containerSize: \'' . $this->params->getValue('container-size') . '\'';
                            }
                            break;
                        case 'container-position':
                            $options[] = 'containerPosition: \'' . $this->params->getValue('container-position') . '\'';
                            break;
                        case 'container-opacity':
                            $options[] = 'containerOpacity: ' . $this->params->getValue('container-opacity');
                            break;
                        case 'container-speed':
                            $options[] = 'containerSpeed: ' . $this->params->getValue('container-speed');
                            break;
                        case 'container-padding':
                            $options[] = 'containerPadding: ' . $this->params->getValue('container-padding');
                            break;
                        case 'preserve':
                            $options[] = 'preserve: \'' . $this->params->getValue('preserve') . '\'';
                            break;
                        case 'loading-text':
                            $options[] = 'loadingText: \'' . $this->params->getValue('loading-text') . '\'';
                            break;
                        case 'start':
                            $options[] = 'start: ' . $this->params->getValue('start');
                            break;
                        case 'z-index':
                            $options[] = 'zIndex: ' . $this->params->getValue('z-index');
                            break;
                        case 'links-window':
                            $options[] = 'linksWindow: \'' . $this->params->getValue('links-window') . '\'';
                            break;
                        case 'pause':
                            $options[] = 'pause: \'' . $this->params->getValue('pause') . '\'';
                            break;
                    }
                }
            }
            if(count($options)) {
                $options = '<script type="text/javascript">MagicSlideshow.extraOptions.' . $id . ' = {' . implode(',', $options) . '};</script>';
            } else {
                $options = '';
            }
            return $options;
        }

        function _paramDefaults() {
            $params = array("right-thumb-max-width"=>array("id"=>"right-thumb-max-width","group"=>"Positioning and Geometry","order"=>"30","default"=>"155","label"=>"Maximum width of right column boxes thumbnail (in pixels)","type"=>"num"),"right-thumb-max-height"=>array("id"=>"right-thumb-max-height","group"=>"Positioning and Geometry","order"=>"31","default"=>"155","label"=>"Maximum height of right column boxes thumbnail (in pixels)","type"=>"num"),"left-thumb-max-width"=>array("id"=>"left-thumb-max-width","group"=>"Positioning and Geometry","order"=>"40","default"=>"155","label"=>"Maximum width of left column boxes thumbnail (in pixels)","type"=>"num"),"left-thumb-max-height"=>array("id"=>"left-thumb-max-height","group"=>"Positioning and Geometry","order"=>"41","default"=>"155","label"=>"Maximum height of left column boxes thumbnail (in pixels)","type"=>"num"),"home-thumb-max-width"=>array("id"=>"home-thumb-max-width","group"=>"Positioning and Geometry","order"=>"50","default"=>"560","label"=>"Maximum width of home boxes image (in pixels)","type"=>"num"),"home-thumb-max-height"=>array("id"=>"home-thumb-max-height","group"=>"Positioning and Geometry","order"=>"51","default"=>"560","label"=>"Maximum height of home boxes image (in pixels)","type"=>"num"),"home-selector-thumb-max-width"=>array("id"=>"home-selector-thumb-max-width","group"=>"Positioning and Geometry","order"=>"60","default"=>"60","label"=>"Maximum width of thumbs in home box (in pixels)","type"=>"num"),"home-selector-thumb-max-height"=>array("id"=>"home-selector-thumb-max-height","group"=>"Positioning and Geometry","order"=>"61","default"=>"60","label"=>"Maximum height of thumbs in home box (in pixels)","type"=>"num"),"square-images"=>array("id"=>"square-images","group"=>"Positioning and Geometry","order"=>"310","default"=>"disable","label"=>"Create square images","description"=>"If enabled then the white/transparent padding will be added around the image","type"=>"array","subType"=>"radio","values"=>array("enable","disable"),"scope"=>"profile"),"container-size"=>array("id"=>"container-size","group"=>"Thumbnails","order"=>"10","default"=>"0","label"=>"Size of thumbnail container (pixels)","description"=>"0 - auto","type"=>"num","scope"=>"tool"),"thumbnails"=>array("id"=>"thumbnails","group"=>"Thumbnails","order"=>"20","default"=>"off","label"=>"Position of thumbnails","type"=>"array","subType"=>"select","values"=>array("inside","outside","off"),"scope"=>"tool"),"thumbnail-opacity"=>array("id"=>"thumbnail-opacity","group"=>"Thumbnails","order"=>"30","default"=>"0.8","label"=>"Opacity of thumbnails (0-1)","type"=>"float","scope"=>"tool"),"container-position"=>array("id"=>"container-position","group"=>"Thumbnails","order"=>"40","default"=>"top","label"=>"Container position","description"=>"Position of thumbnail container","type"=>"array","subType"=>"select","values"=>array("top","right","bottom","left"),"scope"=>"tool"),"container-opacity"=>array("id"=>"container-opacity","group"=>"Thumbnails","order"=>"50","default"=>"0.3","label"=>"Container opacity","description"=>"Opacity of thumbnail container","type"=>"float","scope"=>"tool"),"container-padding"=>array("id"=>"container-padding","group"=>"Thumbnails","order"=>"60","default"=>"0","label"=>"Container padding","description"=>"Padding between container and slideshow (pixels)","type"=>"num","scope"=>"tool"),"container-speed"=>array("id"=>"container-speed","group"=>"Thumbnails","order"=>"75","default"=>"10","label"=>"Container speed","description"=>"Speed that thumbnails move (pixles per second)","type"=>"num","scope"=>"tool"),"use-effect-on-bestsellers-box"=>array("id"=>"use-effect-on-bestsellers-box","group"=>"Miscellaneous","order"=>"30","default"=>"No","label"=>"Use effect on 'bestsellers' box","type"=>"array","subType"=>"select","values"=>array("Yes","No")),"use-effect-on-special-box"=>array("id"=>"use-effect-on-special-box","group"=>"Miscellaneous","order"=>"40","default"=>"No","label"=>"Use effect on 'specials' box","type"=>"array","subType"=>"select","values"=>array("Yes","No")),"use-effect-on-featured-box"=>array("id"=>"use-effect-on-featured-box","group"=>"Miscellaneous","order"=>"50","default"=>"No","label"=>"Use effect on 'featured products' box","type"=>"array","subType"=>"select","values"=>array("Yes","No")),"use-effect-on-latest-box"=>array("id"=>"use-effect-on-latest-box","group"=>"Miscellaneous","order"=>"60","default"=>"No","label"=>"Use effect on 'latest products' box","type"=>"array","subType"=>"select","values"=>array("Yes","No")),"link-to-product-page"=>array("id"=>"link-to-product-page","group"=>"Miscellaneous","order"=>"70","default"=>"Yes","label"=>"Link enlarged image to the product page","type"=>"array","subType"=>"select","values"=>array("Yes","No")),"show-message"=>array("id"=>"show-message","group"=>"Miscellaneous","order"=>"200","default"=>"Yes","label"=>"Show message under slideshow?","type"=>"array","subType"=>"radio","values"=>array("Yes","No")),"message"=>array("id"=>"message","group"=>"Miscellaneous","order"=>"210","default"=>"","label"=>"Message under Slideshow","type"=>"text"),"links-window"=>array("id"=>"links-window","group"=>"Miscellaneous","order"=>"220","default"=>"_self","label"=>"Page where a link should load","type"=>"array","subType"=>"select","values"=>array("_self","_blank","_parent","_top"),"scope"=>"tool"),"preserve"=>array("id"=>"preserve","group"=>"Miscellaneous","order"=>"230","default"=>"Yes","label"=>"Preserve image sizes","description"=>"Use original image proportions or stretch them to same size","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"start"=>array("id"=>"start","group"=>"Miscellaneous","order"=>"240","default"=>"1","label"=>"Starting image number","description"=>"Choose which image to start slideshow at","type"=>"num","scope"=>"tool"),"z-index"=>array("id"=>"z-index","group"=>"Miscellaneous","order"=>"80","default"=>"100","label"=>"Starting z-Index","description"=>"Adjust the stack position above/below other elements","type"=>"num"),"loading-text"=>array("id"=>"loading-text","group"=>"Miscellaneous","order"=>"260","default"=>"","label"=>"Loading text","description"=>"Display text with the loading icon (empty by default)","type"=>"text","scope"=>"tool"),"imagemagick"=>array("id"=>"imagemagick","group"=>"Miscellaneous","order"=>"550","default"=>"off","label"=>"Path to Imagemagick binaries (convert tool)","description"=>"You can set 'auto' to automatically detect imagemagick location or 'off' to disable imagemagick and use php GD lib instead","type"=>"text","scope"=>"profile"),"image-quality"=>array("id"=>"image-quality","group"=>"Miscellaneous","order"=>"560","default"=>"100","label"=>"Quality of thumbnails and watermarked images","type"=>"num","scope"=>"profile"),"use-original-file-names"=>array("id"=>"use-original-file-names","group"=>"Miscellaneous","order"=>"565","default"=>"No","label"=>"Whether to use original file name for cached images","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"profile"),"watermark"=>array("id"=>"watermark","group"=>"Watermark","order"=>"10","default"=>"","label"=>"Path to watermark image","description"=>"Relative for site base path. Use empty to disable watermark","type"=>"text","scope"=>"profile"),"watermark-max-width"=>array("id"=>"watermark-max-width","group"=>"Watermark","order"=>"20","default"=>"50%","label"=>"Maximum width of watermark image","description"=>"pixels (fixed size) or percent (relative for image size)","type"=>"text","scope"=>"profile"),"watermark-max-height"=>array("id"=>"watermark-max-height","group"=>"Watermark","order"=>"21","default"=>"50%","label"=>"Maximum height watermark image","description"=>"pixels (fixed size) or percent (relative for image size)","type"=>"text","scope"=>"profile"),"watermark-opacity"=>array("id"=>"watermark-opacity","group"=>"Watermark","order"=>"40","default"=>"50","label"=>"Opacity of the watermark image","description"=>"0-100","type"=>"num","scope"=>"profile"),"watermark-position"=>array("id"=>"watermark-position","group"=>"Watermark","order"=>"50","default"=>"center","label"=>"Position of the watermark","description"=>"'watermark-size' will ignore when 'watermark-position' sets to 'stretch'","type"=>"array","subType"=>"select","values"=>array("top","right","bottom","left","top-left","bottom-left","top-right","bottom-right","center","stretch"),"scope"=>"profile"),"watermark-offset-x"=>array("id"=>"watermark-offset-x","group"=>"Watermark","order"=>"60","default"=>"0","label"=>"Watermark horizontal offset","description"=>"Offset from left and/or right image borders. Pixels (fixed size) or percent (relative for image size)","type"=>"text","scope"=>"profile"),"watermark-offset-y"=>array("id"=>"watermark-offset-y","group"=>"Watermark","order"=>"70","default"=>"0","label"=>"Watermark vertical offset","description"=>"Offset from top and/or bottom image borders. Pixels (fixed size) or percent (relative for image size)","type"=>"text","scope"=>"profile"),"width"=>array("id"=>"width","group"=>"Slideshow","order"=>"10","default"=>"0","label"=>"Slideshow width (pixels)","description"=>"0 - auto","type"=>"num","scope"=>"tool"),"height"=>array("id"=>"height","group"=>"Slideshow","order"=>"20","default"=>"0","label"=>"Slideshow height (pixels)","description"=>"0 - auto","type"=>"num","scope"=>"tool"),"loop"=>array("id"=>"loop","group"=>"Slideshow","order"=>"30","default"=>"Yes","label"=>"Restart slideshow type","description"=>"Restart slideshow after last image","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"loop-type"=>array("id"=>"loop-type","group"=>"Slideshow","order"=>"40","default"=>"next","label"=>"Loop type","description"=>"Continue to next image or slide all the way back","type"=>"array","subType"=>"radio","values"=>array("first","next"),"scope"=>"tool"),"speed"=>array("id"=>"speed","group"=>"Slideshow","order"=>"50","default"=>"5","label"=>"Slideshow speed","description"=>"Change the slide speed in seconds (0 = manual)","type"=>"num","scope"=>"tool"),"pause"=>array("id"=>"pause","group"=>"Slideshow","order"=>"51","default"=>"off","label"=>"Pause slideshow","type"=>"array","subType"=>"select","values"=>array("hover","click","thumbnail-click","off"),"scope"=>"tool"),"arrows"=>array("id"=>"arrows","group"=>"Slideshow","order"=>"60","default"=>"Yes","label"=>"Show arrows","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"effect-jump"=>array("id"=>"effect-jump","group"=>"Sliding effect","order"=>"10","default"=>"fade","label"=>"Effect when thumbnail is clicked","type"=>"array","subType"=>"select","values"=>array("scroll","fade","default"),"scope"=>"tool"),"effect"=>array("id"=>"effect","group"=>"Sliding effect","order"=>"20","default"=>"scroll","label"=>"Effect for image sliding","type"=>"array","subType"=>"select","values"=>array("scroll","fade","default"),"scope"=>"tool"),"effect-next"=>array("id"=>"effect-next","group"=>"Sliding effect","order"=>"30","default"=>"scroll","label"=>"Effect when arrow is clicked","type"=>"array","subType"=>"select","values"=>array("scroll","fade","default"),"scope"=>"tool"),"effect-duration"=>array("id"=>"effect-duration","group"=>"Sliding effect","order"=>"40","default"=>"1.0","label"=>"Duration of effects (seconds)","type"=>"float","scope"=>"tool"),"direction"=>array("id"=>"direction","group"=>"Sliding effect","order"=>"50","default"=>"right","label"=>"Direction of slide","type"=>"array","subType"=>"select","values"=>array("top","right","bottom","left"),"scope"=>"tool"),"show-text"=>array("id"=>"show-text","group"=>"Text","order"=>"5","default"=>"Yes","label"=>"Whether to show text on slideshow","type"=>"array","subType"=>"radio","values"=>array("Yes","No")),"text"=>array("id"=>"text","group"=>"Text","order"=>"10","default"=>"effect","label"=>"Text shows","description"=>"Display text always or give it an effect","type"=>"array","subType"=>"radio","values"=>array("effect","always"),"scope"=>"tool"),"text-delay"=>array("id"=>"text-delay","group"=>"Text","order"=>"20","default"=>"0.5","label"=>"Text shows delay","description"=>"Delay before the text effect starts","type"=>"float","scope"=>"tool"),"text-opacity"=>array("id"=>"text-opacity","group"=>"Text","order"=>"30","default"=>"0.6","label"=>"Text opacity","description"=>"Opacity of text and background","type"=>"float","scope"=>"tool"),"text-effect"=>array("id"=>"text-effect","group"=>"Text","order"=>"40","default"=>"fixed","label"=>"Text effect","description"=>"Choice of text effects","type"=>"array","subType"=>"select","values"=>array("fixed","slide","fade"),"scope"=>"tool"),"text-position"=>array("id"=>"text-position","group"=>"Text","order"=>"41","default"=>"bottom","label"=>"Text position","description"=>"Position of text (must be different to 'container-position')","type"=>"array","subType"=>"select","values"=>array("top","bottom"),"scope"=>"tool"));
            $this->params->appendArray($params);
        }
    }

}
?>
