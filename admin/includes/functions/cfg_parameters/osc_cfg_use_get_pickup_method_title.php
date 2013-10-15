<?php
/*
  $Id: osc_cfg_use_get_pickup_method_title.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_use_get_pickup_method_title($id) {
    global $osC_Database, $osC_Language;
    
    $pickup_methods = array(
			array('id' => '01', 'text' => $osC_Language->get('shipping_ups_daily_pickup')),
			array('id' => '03', 'text' => $osC_Language->get('shipping_ups_customer_counter')),
			array('id' => '06', 'text' => $osC_Language->get('shipping_ups_one_time_pickup')),
			array('id' => '07', 'text' => $osC_Language->get('shipping_ups_on_call_air_pickup')),
			array('id' => '19', 'text' => $osC_Language->get('shipping_ups_letter_center')),
			array('id' => '20', 'text' => $osC_Language->get('shipping_ups_air_service_center')),
			array('id' => '11', 'text' => $osC_Language->get('shipping_ups_suggested_retail_ratesr'))
		);
    
    $title = null;
    foreach ($pickup_methods as $method) {
    	if ($method['id'] == $id) {
    		$title =  $method['text'];
    		break;
    	}
    }
    
    if ($title === null) {
    	$title = $pickup_methods[0]['text'];
    }

    return $title;
  }
?>
