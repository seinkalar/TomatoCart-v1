<?php
/*
  $Id: osc_cfg_set_ups_quote_type_pull_down_menu.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  function osc_cfg_set_ups_quote_type_pull_down_menu($default, $key = null) {
    global $osC_Language;
    
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');
    
    $quote_types = array(
			array('id' => 'residential', 'text' => $osC_Language->get('shipping_ups_quote_residential')),
			array('id' => 'commercial', 'text' => $osC_Language->get('shipping_ups_quote_commercial'))
		);
   
    $control = array();
    $control['name'] = $name;
    $control['type'] = 'combobox';
    $control['mode'] = 'local';
    $control['values'] = $quote_types;

    return $control;
  }
?>
