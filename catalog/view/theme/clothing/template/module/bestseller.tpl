<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">
    <ul class="bestSeller">
      <?php $i=0; foreach ($products as $product) { $i++; ?>
      <li>
      	<h4 class="name">
        <a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>">
        <?php 
			if($i==1){
				echo "<span class=\"best\">1</span>";	
			}elseif($i==2){
				echo "<span class=\"best\">2</span>";	
			}elseif($i==3){
				echo "<span class=\"best\">3</span>";	
			}else{
				echo "<span class=\"grey\">" . $i . "</span>";
			}
		
		?>
		<?php if(strlen($product['name']) > 15): ?>
				<?php echo substr($product['name'],0,15).'...'; ?>
        <?php else: ?>
        		<?php echo $product['name']; ?>
		<?php endif; ?></a>
       	</h4>
       
        <div class="details">
        	<div class="dWrap">
			   <?php if ($product['thumb']) { ?>
                    <a class="image" href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                <?php } ?>
               <?php if ($product['price']) { ?>
                    <p class="price">
                      <?php if (!$product['special']) { ?>
                      <?php echo $product['price']; ?>
                      <?php } else { ?>
                      <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
                      <?php } ?>
                    </p>
                <?php } ?>            
                <?php if ($product['rating']) { ?>
                    <div class="rating"><img src="catalog/view/theme/dropshipper/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
                <?php } ?>
                <div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><span><?php echo $button_cart; ?></span></a></div>
            </div>
        </div>
      </li>
      <?php } ?>
      
    </ul>
  </div>
  <div class="clear"></div>
</div>
