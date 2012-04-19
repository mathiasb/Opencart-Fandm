<div class="footer">
<ul class="informations">      
				  <?php $informations = $this->model_catalog_information->getInformations(); //Get Information Module
                        foreach($informations as $information): 
                  ?>
                        <li><a href="<?php echo $this->url->link('information/information', 'information_id=' . $information["information_id"]); ?>"><?php echo $information["title"]; ?></a></li>
                 <?php endforeach; //End Information Module ?>
   			</ul>     
         <div class="clear"></div>
          
</div>
       <!-- 
    OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donatation.
    Please donate via PayPal to donate@opencart.com
    //-->
          <p class="powered">Powered by <a href="http://www.opencart.com/">OpenCart</a>. Theme by <a href="http://themeforest.net/user/raviG/portfolio?ref=raviG" class="raviG">raviG</a></p>
       <!-- 
    OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donatation.
    Please donate via PayPal to donate@opencart.com
    //-->  

</body></html>