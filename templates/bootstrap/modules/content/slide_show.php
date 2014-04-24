<?php
/**
 * TomatoCart Open Source Shopping Cart Solution
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v3 (2007)
 * as published by the Free Software Foundation.
 *
 * @package      TomatoCart
 * @author       TomatoCart Dev Team
 * @copyright    Copyright (c) 2009 - 2012, TomatoCart. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl.html
 * @link         http://tomatocart.com
 * @since        Version 1.1.8
 * @filesource
*/
    global $osC_Database, $osC_Language;

		$Qimages = $osC_Database->query('select image ,image_url, description from :table_slide_images si inner join :table_slide_images_to_stores si2s on (si.image_id = si2s.image_id) where si2s.stores_id = :stores_id and language_id =:language_id and status = 1 order by sort_order desc');
		$Qimages->bindTable(':table_slide_images', TABLE_SLIDE_IMAGES);
		$Qimages->bindTable(':table_slide_images_to_stores', TABLE_SLIDE_IMAGES_TO_STORES);
		$Qimages->bindInt(':language_id', $osC_Language->getID());
		$Qimages->bindInt(':stores_id', STORE_ID);
		$Qimages->setCache('slide-images-' . STORE_ID . '-' . $osC_Language->getCode());
		$Qimages->execute();
    
    $images = array();
    while($Qimages->next()) {
        $images[] = array(
            'url' => $Qimages->value('image_url'),
            'image' => $Qimages->value('image'),
            'description' => $Qimages->value('description')
        );
    }
    
    
?>

<?php 
    if (sizeof($images) > 0) {
?>
	<div class="carousel slide" id="myCarousel"> 
        <ol class="carousel-indicators">
        	<?php 
        	    for ($i = 0; $i < sizeof($images); $i++) {
        	        echo '<li data-slide-to="' . $i . '" data-target="#myCarousel"' . (($i == 0) ? ' class="active"' : '') . '></li>';
        	    }
        	?>
        </ol>
		<div class="carousel-inner">
			<?php 
        	    for ($i = 0; $i < sizeof($images); $i++) {
			?>
            <div class="item<?php echo ($i == 0) ? ' active' : '';?>">
                <?php echo osc_link_object(osc_href_link($images[$i]['url']), osc_image(DIR_WS_IMAGES . $images[$i]['image'], $images[$i]['description'])); ?>
                
                <?php 
                    if (MODULE_CONTENT_SLIDE_SHOW_DISPLAY_INFO == 'True') {
                        echo '<div class="carousel-caption"><p>' . $images[$i]['description'] . '</p></div>';
                    }
                ?>
            </div>			
			<?php 
			    }
			?>
		</div>
        <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
  		<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
	</div>
<?php 
    }
?>