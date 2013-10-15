<?php
/*
  $Id: osc_cfg_set_ups_packaging_pull_down_menu.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_ups_origin_pull_down_menu($default, $key = null) {
    global $osC_Language;
    
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');
    
    $origins = array(
			array('id' => 'US', 'text' => $osC_Language->get('shipping_ups_origin_us')),
			array('id' => 'CA', 'text' => $osC_Language->get('shipping_ups_origin_ca')),
			array('id' => 'EU', 'text' => $osC_Language->get('shipping_ups_origin_eu')),
			array('id' => 'PR', 'text' => $osC_Language->get('shipping_ups_origin_pr')),
			array('id' => 'MX', 'text' => $osC_Language->get('shipping_ups_origin_mx')),
			array('id' => 'other', 'text' => $osC_Language->get('shipping_ups_origin_other'))
		);
   
    $control = array();
    $control['name'] = $name;
    $control['type'] = 'combobox';
    $control['mode'] = 'local';
    $control['values'] = $origins;

    return $control;
  }
?>
