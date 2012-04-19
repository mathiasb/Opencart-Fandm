<?php echo $header; ?>
<div id="content">
<ul class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
</ul>
  <h1><?php echo $heading_title; ?></h1>
  <?php echo $description; ?>
  <div class="buttons">
    <a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_continue; ?></span></a>
  </div>
  </div>
  <div class="clear"></div>
<?php echo $content_bottom; ?>
<?php echo $footer; ?></div>
