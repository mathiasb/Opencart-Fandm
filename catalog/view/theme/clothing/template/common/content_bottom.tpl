<div class="contentBottom">
<div class="welcomeMod">
	<?php 
        //Welcome Text
        $welcome = $this->config->get('welcome_module');
        echo html_entity_decode($welcome[1]["description"][1]);
    ?>
</div>   

<?php foreach ($modules as $module): ?>
	<?php echo $module; ?>    
<?php endforeach; ?>

<div class="clear"></div>
</div>
