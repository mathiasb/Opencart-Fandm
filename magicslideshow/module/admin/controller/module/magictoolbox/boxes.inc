<?php
$this->render();
$module_name = strtolower(str_replace('ControllerModule','',get_class($this)));
if (!isset($results) && isset($products)) {
	$results = $product_infos;
} else if (!isset($results) && !isset($products)){
	$results=false;
}
if (VERSION >= 1.5) {
	if (isset($module)) {
		$id = $module_name.'_'.$this->config->get($module_name.'_'.$module.'_position');
	} else {
		$id = $module_name.'_'.$setting['position'];
	}
} else {
	$id = $this->id.'_'.$this->config->get($this->id.'_position');
}
$this->output = MagicSlideshow($this->output,$this,$id,$results);
?>