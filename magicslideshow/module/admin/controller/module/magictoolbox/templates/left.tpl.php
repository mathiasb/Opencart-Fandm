<?php
$m = intval(self::$options->getValue('selectors-margin'));
$ws = intval(self::$options->getValue('selector-max-width'));
$wm = intval(self::$options->getValue('thumb-max-width'));
$hm = intval(self::$options->getValue('thumb-max-height'));
?>
<!-- Begin magicslideshow -->
<div class="MagicToolboxContainer selectorsLeft" style="width: <?php echo ($wm + $ws + $m)?>px">
    <?php if(count($thumbs)):?>
    <div id="MagicToolboxSelectors<?php echo $pid?>" class="MagicToolboxSelectorsContainer<?php echo $magicscroll;?>" style="width: <?php echo $ws?>px;margin-right: <?php echo $m?>px;">
        <?php echo join("\n\t",$thumbs)?>
    </div>
    <?php endif?>
    <div class="MagicToolboxMainContainer" style="width: <?php echo $wm?>px">
        <?php echo $main?>
        <?php if(isset($message)):?>
            <div class="MagicToolboxMessage"><?php echo $message?></div>
        <?php endif?>
        <?php if(!empty($magicscroll)): ?>
            <script type="text/javascript">
                MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?> = MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?> || {};
                MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?>.direction = 'bottom';
                <?php if(self::$options->checkValue('height', 0)): ?>
                MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?>.height = <?php echo $hm?>;
                <?php endif?>
            </script>
        <?php endif?>
    </div>
    <div style="clear:both"></div>

</div>
<!-- End magicslideshow -->
