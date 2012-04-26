<?php include('head.tpl'); ?>
<div id="container">

  <div class="top">
  <?php //Header Cart ?>
  		
 		<?php //Default Opencart Links ?>
        <?php if (!$logged): ?>
             <p class="welcome"><?php echo $text_welcome; ?></p>
        <?php else: ?>
             <p class="welcome"><?php echo $text_logged; ?></p> 
        <?php endif; ?>
        
        <div class="currency">
        	  <?php echo $currency; ?>
        </div>
      
        
        <?php echo $cart; ?>    
              
        <ul class="links">
        	<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
            <li><a href="<?php echo $wishlist; ?>" id="wishlist_total"><?php echo $text_wishlist; ?></a></li>         
            <li><a href="<?php echo $shopping_cart; ?>"><?php echo $text_shopping_cart; ?></a></li>
            <li><a href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></li>
        </ul> 
        
        <div class="clear"></div>
        
  <?php echo $language; ?>
  </div>
<div id="header">
  <?php //Logo ?>
  <?php 
  		$page = '';
  		if(isset($this->request->get['route'])){
  			$page = $this->request->get['route'];
		}
		if($page == "common/home" || $page==''):
  ?>
  	  <?php if ($logo): ?>
            <h1 id="logo"><a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a></h1>
      <?php else: ?>
            <h1><a href="<?php echo $home; ?>"><?php echo $name; ?></a></h1>
      <?php endif; ?>
  <?php else: ?>
  	   <?php if($logo): ?>
       		<a id="logo" href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /><span><?php echo $name; ?></span></a>
       <?php else: ?>
       		<a href="<?php echo $home; ?>"><?php echo $name; ?></a>
       <?php endif; ?>
  <?php endif; //End Logo ?>


    <div id="search">
        <?php if ($filter_name) { ?>
        <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" />
        <?php } else { ?>
        <input type="text" name="filter_name" value="<?php echo $text_search; ?>" onclick="this.value = '';" onkeydown="this.style.color = '#000000';" />
        <?php } ?>
        <div class="button-search"></div>
    </div><?php // END SEARCH DIV ?>


 <ul class="categories">
                  <?php if ($categories) { //Get Categories ?>
                  <ul class="first">
                    <?php foreach ($categories as $category) { ?>
                    <li class="level0"><a href="<?php echo $category['href']; ?>" class="level-top"><?php echo $category['name']; ?></a>
                      <?php if ($category['children']) { ?>
                        <?php for ($i = 0; $i < count($category['children']);) { ?>
                        <ul>
							  <?php $j = $i + ceil(count($category['children']) / $category['column']);?>
                              <?php for (; $i < $j; $i++) { ?>
                              <?php if (isset($category['children'][$i])) { ?>
                              <li <?php if($i+1==$j){echo 'class="last"';} ?>><a href="<?php echo $category['children'][$i]['href']; ?>"><?php echo $category['children'][$i]['name']; ?></a></li>
                              <?php } ?>
                              <?php } ?>
                        </ul>
                        <?php } ?>
                      <?php } ?>
                    </li>
                    <?php } ?>
                   </ul>
                <?php } //End Categories ?>
            
        </ul>
         <div class="clear"></div> 
  </div>



  

  