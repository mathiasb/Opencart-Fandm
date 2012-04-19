<div class="latest">
  <?php $oddEven = array('odd', 'even'); ?>
 
  <ul class="items">
      <?php $i=0; foreach ($products as $product) { $i++?>
      <li <?php if($i%3==0 && $i!=0){echo 'class="last"'; } ?>>
        <?php if ($product['thumb']): ?>
       		 <a class="image" href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" width="240" height="244" /></a>
        <?php endif; ?>
        <div class="details">
        	<div class="bottom">
                <h3 class="name"><?php 
					if(strlen($product['name']) > 15){ 
                        echo substr($product['name'],0,15).'...';
                	}else{
						echo $product['name'];	
					}
                ?></h3>
                <?php if ($product['price']): ?>
                <p class="price">
                  <?php if (!$product['special']): ?>
                        <?php echo $product['price']; ?>
                  <?php else: ?>
                  <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
                  <?php endif; ?>
                </p>
                <?php endif; ?>
            </div>
            <div class="clear"></div>      
        </div>
      </li>
      
      <?php } ?>	
</ul>
<div class="clear"></div>
</div>
