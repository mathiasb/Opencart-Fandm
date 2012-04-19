<?php if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>"><head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/clothing/stylesheet/styles.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/theme/clothing/stylesheet/slideshow.css" />
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
<script type="text/javascript" src="catalog/view/theme/clothing/js/cycle.js"></script>
<script type="text/javascript" src="catalog/view/theme/clothing/js/custom.js"></script>
<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow&v2' rel='stylesheet' type='text/css'>
<?php echo $google_analytics; ?>
</head>
<body <?php 		
	$page = '';
	
	if(isset($this->request->get['route'])){
		$page = $this->request->get['route'];
	}
	if($page == "common/home" || $page == ''){
		echo 'class="home"';
	}elseif($page == "product/category" || $page == "product/manufacturer/product"){
		$titleName = explode(' ',$title);
		$page = $titleName[0];	
		echo 'class="' . strtolower($page) . " category" . '"';		
	}elseif($page == "product/product"){
		$titleName = explode(' ',$title);
		$page = $titleName[0];	
		echo 'class="' . strtolower($page) . " product_page" . '"';		
	}elseif($page == 'checkout/cart'){
		echo 'class="shopping_cart"';
	}elseif($page == 'product/search'){
		echo 'class="' . "search" . '"';
	}elseif($page == 'product/special'){
		echo 'class="' . "special_offers" . '"';
	}elseif($page !== "common/home"){
		$titleName = explode(' ',$title);
		$page = $titleName[0];	
			if(isset($titleName[1])){
				$page = $titleName[0] . "_" . $titleName[1];
			}
		echo 'class="' . strtolower($page) . '"';				
	}
?>>