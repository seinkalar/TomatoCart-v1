<?php
/*
  $Id: osc_cfg_use_get_origin_title.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_use_get_origin_title($id) {
    global $osC_Database, $osC_Language;
    
    $origins = array(
			array('id' => 'US', 'text' => $osC_Language->get('shipping_ups_origin_us')),
			array('id' => 'CA', 'text' => $osC_Language->get('shipping_ups_origin_ca')),
			array('id' => 'EU', 'text' => $osC_Language->get('shipping_ups_origin_eu')),
			array('id' => 'PR', 'text' => $osC_Language->get('shipping_ups_origin_pr')),
			array('id' => 'MX', 'text' => $osC_Language->get('shipping_ups_origin_mx')),
			array('id' => 'other', 'text' => $osC_Language->get('shipping_ups_origin_other'))
		);
    
    $title = null;
    foreach ($origins as $origin) {
    	if ($origin['id'] == $id) {
    		$title =  $origin['text'];
    		break;
    	}
    }
    
    if ($title === null) {
    	$title = $origins[0]['text'];
    }

    return $title;
  }
?>
