<?php
/*
  $Id: store.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  Copyright (c) 2009 TomatoCart

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

class toC_Json_Store {
	function listStores() {
		global $toC_Json, $osC_Database;
	
		$Qstores = $osC_Database->query('select * from :table_store order by store_id');
		$Qstores->bindTable(':table_store', TABLE_STORE);
		$Qstores->execute();
	
		$records = array();
		while ($Qstores->next()) {
			$records[] = array(
				'store_id' => $Qstores->ValueInt('store_id'),
				'store_name' => $Qstores->Value('store_name'),
				'url_address' => $Qstores->Value('url_address'),
				'ssl_url_address' => $Qstores->Value('ssl_url_address'),
			);
		}
		
		$Qstores->freeResult();
	
		$response = array(EXT_JSON_READER_TOTAL => sizeof($records), EXT_JSON_READER_ROOT => $records);
	
		echo $toC_Json->encode($response);
	}
}