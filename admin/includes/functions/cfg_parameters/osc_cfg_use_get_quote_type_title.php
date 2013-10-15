<?php
/*
  $Id: osc_cfg_use_get_quote_type_title.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_use_get_quote_type_title($id) {
    global $osC_Language;
    
		$quote_types = array(
			array('id' => 'residential', 'text' => $osC_Language->get('shipping_ups_quote_residential')),
			array('id' => 'commercial', 'text' => $osC_Language->get('shipping_ups_quote_commercial'))
		);
    
    $title = null;
    foreach ($quote_types as $type) {
    	if ($type['id'] == $id) {
    		$title =  $type['text'];
    		break;
    	}
    }
    
    if ($title === null) {
    	$title = $quote_types[0]['text'];
    }

    return $title;
  }
?>
