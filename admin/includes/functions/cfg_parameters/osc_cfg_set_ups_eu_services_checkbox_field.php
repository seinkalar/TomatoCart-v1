<?php
/*
  $Id: osc_cfg_set_ups_eu_services_checkbox_field.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_ups_eu_services_checkbox_field($default, $key = null) {
  	global $osC_Language;
  	
    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . '][]';
    
    $services = array(
    	array('id' => '07', 'text' => $osC_Language->get('shipping_ups_express')),
			array('id' => '08', 'text' => $osC_Language->get('shipping_ups_expedited')),
			array('id' => '11', 'text' => $osC_Language->get('shipping_ups_standard')),
			array('id' => '54', 'text' => $osC_Language->get('shipping_ups_worldwide_express_plus')),
			array('id' => '65', 'text' => $osC_Language->get('shipping_ups_saver')),
			array('id' => '82', 'text' => $osC_Language->get('shipping_ups_today_standard')),
			array('id' => '83', 'text' => $osC_Language->get('shipping_ups_today_dedicated_courier')),
			array('id' => '84', 'text' => $osC_Language->get('shipping_ups_today_intercity')),
			array('id' => '85', 'text' => $osC_Language->get('shipping_ups_today_express')),
			array('id' => '86', 'text' => $osC_Language->get('shipping_ups_today_express_saver')),
			array('id' => '01', 'text' => $osC_Language->get('shipping_ups_next_day_air')), 
			array('id' => '02', 'text' => $osC_Language->get('shipping_ups_2nd_day_air')),
			array('id' => '03', 'text' => $osC_Language->get('shipping_ups_ground')),
			array('id' => '03', 'text' => $osC_Language->get('shipping_ups_next_day_air_early_am'))
		);
    
    $control = array();
    $control['name'] = $name;
    $control['type'] = 'checkbox';
    $control['values'] = $services;

    return $control;
  }
?>
