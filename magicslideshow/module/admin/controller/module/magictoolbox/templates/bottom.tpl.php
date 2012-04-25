<?php
$m = intval(self::$options->getValue('selectors-margin'));
$wm = intval(self::$options->getValue('thumb-max-width'));
?>

<!-- Begin magicslideshow -->
<div class="MagicToolboxContainer" style="width: <?php echo $wm?>px">
    <?php echo $main?>

    <?php if(isset($message)):?>
        <div class="MagicToolboxMessage"><?php echo $message?></div>
    <?php endif?>

    <?php if(count($thumbs)):?>
    <div id="MagicToolboxSelectors<?php echo $pid?>" class="MagicToolboxSelectorsContainer<?php echo $magicscroll;?>" style="margin-top: <?php echo $m;?>px">
        <?php echo join("\n\t",$thumbs)?>
        <div style="clear: both"></div>
    </div>
    <?php if(!empty($magicscroll)): ?>
        <script type="text/javascript">
            MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?> = MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?> || {};
            MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?>.direction = 'right';
            <?php if(self::$options->checkValue('width', 0)): ?>
            MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?>.width = <?php echo $wm?>;
            <?php endif?>
        </script>
    <?php endif?>
    <?php endif?>


</div>
<!-- End magicslideshow -->
