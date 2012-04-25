#######################################################

 Magic Slideshow™
 OpenCart module version v2.8.36 [v1.1.20:v1.1.22]
 
 www.magictoolbox.com
 support@magictoolbox.com

 Copyright 2012 Magic Toolbox

#######################################################

INSTALLATION:

IMPORTANT: Before you start, we recommend you open readme.txt and follow those instructions. It is faster and easier than these readme_manual.txt instructions. If installation failed using the readme.txt procedure, then continue with these instructions instead.

1. Copy the 'admin' folder to your OpenCart directory, keeping the file structure.

2. Backup your /catalog/controller/product/product.php file and open it in a text editor (e.g. Notepad).

3. Find the line that looks like '<?php' and insert after it:

    global $aFolder;
    if (!defined('HTTP_ADMIN')) define('HTTP_ADMIN','admin');
    $aFolder = preg_replace('/.*\/([^\/].*)\//is','$1',HTTP_ADMIN);
    if (!isset($GLOBALS['magictoolbox']['magicslideshow']) && !isset($GLOBALS['magicslideshow_module_loaded'])) {
        include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?'components/com_ayelshop/opencart/':'').$aFolder.'/controller/module/magictoolbox/module.php'; 
    };

4. If your version of OpenCart is lower than '1.5.0', find the lines looking like '$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));'.

5. Replace that code with the following:

    $this->response->setOutput(magicslideshow($this->render(TRUE),$this,'product',$product_info), $this->config->get('config_compression'));

6. If your version of OpenCart is greater than '1.5.0', find the line looking like '$this->response->setOutput($this->render());'.

7. Replace that code with the following:

    $this->response->setOutput(magicslideshow($this->render(TRUE),$this,'product',$product_info), $this->config->get('config_compression'));

8. Find the line that looks like '$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);'.

9. Insert the following line after it:

    $product_info['images'] = $results;

10. Backup your /catalog/controller/product/category.php file and open it in your text editor.

11. Find the line that looks like '<?php' and insert after it:

    global $aFolder;
    if (!defined('HTTP_ADMIN')) define('HTTP_ADMIN','admin');
    $aFolder = preg_replace('/.*\/([^\/].*)\//is','$1',HTTP_ADMIN);
    if (!isset($GLOBALS['magictoolbox']['magicslideshow']) && !isset($GLOBALS['magicslideshow_module_loaded'])) {
        include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?'components/com_ayelshop/opencart/':'').$aFolder.'/controller/module/magictoolbox/module.php'; 
    };

12. If your version of OpenCart is lower than '1.5.0', find the line looking like '$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));'.

13. Replace that code with the following:

    $this->response->setOutput(magicslideshow($this->render(TRUE),$this,'category', $results), $this->config->get('config_compression'));

14. If your version of OpenCart is greater than '1.5.0', find the line looking like '$this->response->setOutput($this->render());'.

15. Replace that code with the following:

    $this->response->setOutput(magicslideshow($this->render(TRUE),$this,'category', $results), $this->config->get('config_compression'));

16. Backup your /catalog/controller/common/home.php file and open it in editor.

17. If your version of OpenCart is lower than '1.5.0', find the line looking like '$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));'.

18. Replace that code with the following:

    $this->render();
    if(version_compare(VERSION, '1.4.9', '<')) {
        $this->output = magicslideshow($this->output,$this,'latest_home_category',$this->model_catalog_product->getLatestProducts(8));
    }
    $this->response->setOutput($this->output, $this->config->get('config_compression'));

19. If your version of OpenCart is greater than '1.5.0', find the line looking like '$this->response->setOutput($this->render());'.

20. Replace that code with the following:

    $this->render();
    if(version_compare(VERSION, '1.4.9', '<')) {
        $this->output = magicslideshow($this->output,$this,'latest_home_category',$this->model_catalog_product->getLatestProducts(8));
    }
    $this->response->setOutput($this->output, $this->config->get('config_compression'));

21. Backup your /catalog/controller/common/header.php file and open it in your text editor.

22. Find the line that looks like '<?php' and insert after it:

    global $aFolder;
    if (!defined('HTTP_ADMIN')) define('HTTP_ADMIN','admin');
    $aFolder = preg_replace('/.*\/([^\/].*)\//is','$1',HTTP_ADMIN);
    if (!isset($GLOBALS['magictoolbox']['magicslideshow']) && !isset($GLOBALS['magicslideshow_module_loaded'])) {
        include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?'components/com_ayelshop/opencart/':'').$aFolder.'/controller/module/magictoolbox/module.php'; 
    };

23. Find the line '$this->render();'.

24. Replace that code with the following:

    $this->render();
    if($this->config->get('magicslideshow_status') != 0) {
        $tool = magicslideshow_load_core_class($this);
        if(use_effect_on($tool)) {
            $this->output = set_headers($this->output);
        }
    };

25. Backup your /catalog/controller/module/latest.php file and open it in your text editor.

26. Find the line that looks like '<?php' and insert after it:

    global $aFolder;
    if (!defined('HTTP_ADMIN')) define('HTTP_ADMIN','admin');
    $aFolder = preg_replace('/.*\/([^\/].*)\//is','$1',HTTP_ADMIN);
    if (!isset($GLOBALS['magictoolbox']['magicslideshow']) && !isset($GLOBALS['magicslideshow_module_loaded'])) {
        include (preg_match("/components\/com_ayelshop\/opencart\//ims",__FILE__)?'components/com_ayelshop/opencart/':'').$aFolder.'/controller/module/magictoolbox/module.php'; 
    };

27. Find the line that looks like '$this->render();'

28. Replace that code with the following:

    global $aFolder; include($aFolder.'/controller/module/magictoolbox/boxes.inc');

29. Repeat the modifications you made to 'latest.php' to these files:

    /catalog/controller/module/bestseller.php
    /catalog/controller/module/special.php
    /catalog/controller/module/featured.php

30. Open /catalog/controller/module/featured.php again and find the line like '$product_info = $this->model_catalog_product->getProduct($product_id);'

31. Add the following code after it :

    $product_infos[] = $product_info;

32. You are done! Now you can open the 'Extensions' page in your OpenCart admin panel to activate and customize the module.



33. You've now installed the demo version of Magic Slideshow!

34. To upgrade, buy Magic Slideshow and overwrite the magicslideshow.js file with the same file from the full version.

Buy a single license here:

http://www.magictoolbox.com/buy/magicslideshow/

