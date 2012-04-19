<div class="specials">
<script type="text/javascript" src="catalog/view/theme/dropshipper/js/countdown.js"></script>
  <p class="heading"><?php echo $heading_title; ?></p>
    <ul class="specialItems">    
      <?php $i=-1; foreach ($products as $product) { $i++ ?>
      <li <?php if($i%2==0 && $i!=0){echo 'class="last"'; } ?>>
        <?php if ($product['thumb']) { ?>
        	<a class="image" href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
        <?php } ?>
        <h3 class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h3>
        <?php if ($product['price']) { ?>
        <p class="price">
          <?php if (!$product['special']) { ?>
          		<?php echo $product['price']; ?>
          <?php } else { ?>
          	<span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>         
       	</p>
        <?php } ?>

		<?php } ?>
        
        <div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><span><?php echo $button_cart; ?></span></a></div>
      </li>
      <?php } ?>
    </ul>
    <div class="clear"></div>
</div>

