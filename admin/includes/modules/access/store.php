<?php
/*
  $Id: store.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 TomatoCart;  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Access_store extends osC_Access {
    var $_module = 'store',
        $_group = 'configuration',
        $_icon = 'photo_add.png',
        $_title,
        $_sort_order = 0;

    function osC_Access_store() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_store_title');
    }
  }
?>
