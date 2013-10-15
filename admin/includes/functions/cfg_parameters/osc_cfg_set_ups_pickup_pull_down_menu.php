<?php
/*
  $Id: osc_cfg_set_ups_pickup_pull_down_menu.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_ups_pickup_pull_down_menu($default, $key = null) {
    global $osC_Language;
    
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');
    
    $pickup_methods = array(
			array('id' => '01', 'text' => $osC_Language->get('shipping_ups_daily_pickup')),
			array('id' => '03', 'text' => $osC_Language->get('shipping_ups_customer_counter')),
			array('id' => '06', 'text' => $osC_Language->get('shipping_ups_one_time_pickup')),
			array('id' => '07', 'text' => $osC_Language->get('shipping_ups_on_call_air_pickup')),
			array('id' => '19', 'text' => $osC_Language->get('shipping_ups_letter_center')),
			array('id' => '20', 'text' => $osC_Language->get('shipping_ups_air_service_center')),
			array('id' => '11', 'text' => $osC_Language->get('shipping_ups_suggested_retail_ratesr'))
		);
   
    $control = array();
    $control['name'] = $name;
    $control['type'] = 'combobox';
    $control['mode'] = 'local';
    $control['values'] = $pickup_methods;

    return $control;
  }
?>
