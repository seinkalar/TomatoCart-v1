<?php
/*
  $Id: ups.php $
  TomatoCart Open Source Shopping Cart Solutions
  http://www.tomatocart.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
  class osC_Shipping_ups extends osC_Shipping {
    var $icon, $countries; 

    var $_title,
        $_code = 'ups',
        $_status = false,
        $_sort_order,
        $_error = false,
        $_error_messages = array(),
        $_service_code = array();

// class constructor
    function osC_Shipping_ups() {
      global $osC_Language;

      $this->icon = DIR_WS_IMAGES . 'icons/shipping_ups.gif';

      $this->_title = $osC_Language->get('shipping_ups_title');
      $this->_description = $osC_Language->get('shipping_ups_description');
      $this->_status = (defined('MODULE_SHIPPING_UPS_STATUS') && (MODULE_SHIPPING_UPS_STATUS == 'Yes') ? true : false);
      $this->_sort_order = (defined('MODULE_SHIPPING_UPS_SORT_ORDER') ? MODULE_SHIPPING_UPS_SORT_ORDER : null);
    }

// class methods
    function initialize() {
      global $osC_Database, $osC_Language;

      $this->tax_class = MODULE_SHIPPING_UPS_TAX_CLASS;

      //check status and shipping zone
      if ( ($this->_status === true) && ((int)MODULE_SHIPPING_UPS_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_SHIPPING_UPS_ZONE);
        $Qcheck->bindInt(':zone_country_id', $osC_ShoppingCart->getShippingAddress('country_id'));
        $Qcheck->execute();

        while ($Qcheck->next()) {
          if ($Qcheck->valueInt('zone_id') < 1) {
            $check_flag = true;
            break;
          } elseif ($Qcheck->valueInt('zone_id') == $osC_ShoppingCart->getShippingAddress('zone_id')) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->_status = false;
        }
      }
      
      /*init service codes for each Shipping Origin*/
      
      //US origin
      $this->service_code['US'] = array(
	    	array('id' => '01', 'text' => $osC_Language->get('shipping_ups_next_day_air')),
				array('id' => '02', 'text' => $osC_Language->get('shipping_ups_2nd_day_air')),
				array('id' => '03', 'text' => $osC_Language->get('shipping_ups_ground')),
				array('id' => '07', 'text' => $osC_Language->get('shipping_ups_worldwide_express')),
				array('id' => '08', 'text' => $osC_Language->get('shipping_ups_worldwide_expedited')),
				array('id' => '11', 'text' => $osC_Language->get('shipping_ups_standard')),
				array('id' => '12', 'text' => $osC_Language->get('shipping_ups_3_day_select')),
				array('id' => '13', 'text' => $osC_Language->get('shipping_ups_next_day_air_saver')),
				array('id' => '14', 'text' => $osC_Language->get('shipping_ups_next_day_air_early_am')),
				array('id' => '54', 'text' => $osC_Language->get('shipping_ups_worldwide_express_plus')),
				array('id' => '59', 'text' => $osC_Language->get('shipping_ups_2nd_day_air_am')),
				array('id' => '65', 'text' => $osC_Language->get('shipping_ups_saver'))
			);
      
      //Canada Origin
      $this->service_code['CA'] = array(
	    	array('id' => '01', 'text' => $osC_Language->get('shipping_ups_express')),
				array('id' => '02', 'text' => $osC_Language->get('shipping_ups_expedited')),
				array('id' => '07', 'text' => $osC_Language->get('shipping_ups_worldwide_express')),
				array('id' => '08', 'text' => $osC_Language->get('shipping_ups_worldwide_expedited')),
				array('id' => '11', 'text' => $osC_Language->get('shipping_ups_standard')),
				array('id' => '12', 'text' => $osC_Language->get('shipping_ups_3_day_select')),
				array('id' => '13', 'text' => $osC_Language->get('shipping_ups_saver')),
				array('id' => '14', 'text' => $osC_Language->get('shipping_ups_next_day_air_early_am')),
				array('id' => '54', 'text' => $osC_Language->get('shipping_ups_worldwide_express_plus')),
				array('id' => '65', 'text' => $osC_Language->get('shipping_ups_saver'))
			);
      
      //European Union Origin
      $this->service_code['EU'] = array(
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
      
      //Puerto Rico Origin
      $this->service_code['PR'] = array(
	    	array('id' => '01', 'text' => $osC_Language->get('shipping_ups_next_day_air')),
				array('id' => '02', 'text' => $osC_Language->get('shipping_ups_2nd_day_air')),
				array('id' => '03', 'text' => $osC_Language->get('shipping_ups_ground')),
				array('id' => '07', 'text' => $osC_Language->get('shipping_ups_worldwide_express')),
				array('id' => '08', 'text' => $osC_Language->get('shipping_ups_worldwide_expedited')),
				array('id' => '14', 'text' => $osC_Language->get('shipping_ups_next_day_air_early_am')),
				array('id' => '54', 'text' => $osC_Language->get('shipping_ups_worldwide_express_plus')),
				array('id' => '65', 'text' => $osC_Language->get('shipping_ups_saver'))
			);
      
      //Mexico Origin
      $this->service_code['MX'] = array(
	    	array('id' => '07', 'text' => $osC_Language->get('shipping_ups_express')),
				array('id' => '08', 'text' => $osC_Language->get('shipping_ups_expedited')),
				array('id' => '54', 'text' => $osC_Language->get('shipping_ups_express_plus')),
				array('id' => '65', 'text' => $osC_Language->get('shipping_ups_saver'))
			);
      
      //All other origins
      $this->service_code['other'] = array(
	    	array('id' => '07', 'text' => $osC_Language->get('shipping_ups_express')),
				array('id' => '08', 'text' => $osC_Language->get('shipping_ups_worldwide_expedited')),
				array('id' => '11', 'text' => $osC_Language->get('shipping_ups_standard')),
				array('id' => '54', 'text' => $osC_Language->get('shipping_ups_worldwide_express_plus')),
				array('id' => '65', 'text' => $osC_Language->get('shipping_ups_saver'))
			);
    }

    function quote() {
    	global $osC_Language, $osC_ShoppingCart, $osC_Weight;
    	
    	//build the api request
    	$xml = $this->_build_qoute_xml();
    	
    	//send the request
    	$result = $this->_send_quote_request($xml);
    	
    	//parse xml response to get the shipping rate
    	$this->quotes = $this->_parse_quote_response($result);
    	
    	//verify whether the weight should be displayed
    	if (MODULE_SHIPPING_UPS_DISPLAY_WEIGHT == 'Yes') {
    		$this->quotes['module'] .= ' (' . $osC_Language->get('shipping_ups_weight') . $osC_Weight->display($osC_ShoppingCart->getWeight(), SHIPPING_WEIGHT_UNIT) . ')';
    	}
    	
    	//shipping icon
    	if (!empty($this->icon)) $this->quotes['icon'] = osc_image($this->icon, $this->_title);
    	
    	//check error
    	if ($this->_error === true) {
    		$this->_set_contact_message();
    	
    		$this->quotes['error'] = '<strong style="color:red;">' . implode('<br />', $this->_error_messages) . '</strong>';
    	}
    	
    	return $this->quotes;
    }
    
    /**
     * Send the api request to get the real-time quote
     *
     * @acess private
     * @param $xml
     * @return  xml
     */
    function _send_quote_request($xml) {
    	$response = '';
    	
    	if (MODULE_SHIPPING_UPS_TEST_MODE == 'Yes') {
    		$api_url = 'https://wwwcie.ups.com/ups.app/xml/Rate';
			} else {
				$api_url = 'https://www.ups.com/ups.app/xml/Rate';
			}
    	
    	//verify whether the curl is supported
    	if (function_exists('curl_init')) {
    		$ch = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $api_url);
    		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
    		curl_setopt($ch, CURLOPT_POST, 1);
    		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    		
    		$response .= curl_exec($ch);
    	
    		// Verify whether there is any curl error thrown
    		if(curl_errno($ch)){
    			$this->_error = true;
    		}
    	
    		curl_close($ch);
    		
    	//send the http get request with socket
    	}else {
    		//parse the service url
    		$server = parse_url($api_url);
    	
    		$fp = fsockopen($server['host'], 80, $errno, $errstr, 30);
    	
    		if (!$fp) {
    			$this->_error = true;
    		}else {
    			$request_line = "POST " . $server['path'] . " HTTP/1.1\r\n";
    			
    			$request_header = "Host:" . $server['host'] . "\r\n";
    			$request_header .= "Content-Type: text/xml\r\n";
    			$request_header .= "Content-Length: " . strlen($xml) . "\r\n";
    			$request_header .= "Connection: Close\r\n\r\n";
    	
    			fwrite($fp, $request_line . $request_header . $xml);
    			while(!feof($fp)) {
    				$response .= fgets($fp, 1024);
    			}
    	
    			fclose($fp);
    		}
    	}
    	
    	return $response;
    }
    
    /**
     * Parse quote response to get the real-time shipping quotes
     *
     * @acess private
     * @param $response
     * @return  array
     */
    function _parse_quote_response($result) {
    	global $osC_Currencies;
    	
    	//init quotes
    	$quotes = array(
				'id' => $this->_code,
				'module' => $this->_title,
    		'methods' => array(),			
				'tax_class_id' => $this->tax_class
			);
    	
    	//get allowed shipping services for current selected origin
    	$allowed_codes = array();
    	switch(MODULE_SHIPPING_UPS_ORIGIN) {
    		case 'US':
    			$allowed_codes = explode(',', MODULE_SHIPPING_UPS_US_SERVICES);
    			break;
    		case 'CA':
    			$allowed_codes = explode(',', MODULE_SHIPPING_UPS_CA_SERVICES);
    			break;
    		case 'EU':
    			$allowed_codes = explode(',', MODULE_SHIPPING_UPS_EU_SERVICES);
    			break;
				case 'PR':
					$allowed_codes = explode(',', MODULE_SHIPPING_UPS_PR_SERVICES);
					break;
				case 'MX':
					$allowed_codes = explode(',', MODULE_SHIPPING_UPS_MX_SERVICES);
					break;
				case 'other':
					$allowed_codes = explode(',', MODULE_SHIPPING_UPS_OTHER_SERVICES);
					break;
				default:
					$allowed_codes = explode(',', MODULE_SHIPPING_UPS_US_SERVICES);
    	}
    	
    	
    	//load the dom based the xml returned from UPS
    	$dom = new DOMDocument('1.0', 'UTF-8');
    	$dom->loadXml($result);
    	
    	//parse the xml to get the quote methods
    	$rating_service_selection_response = $dom->getElementsByTagName('RatingServiceSelectionResponse')->item(0);
    	$response = $rating_service_selection_response->getElementsByTagName('Response')->item(0);
    	
    	$response_status_code = $response->getElementsByTagName('ResponseStatusCode');
    	if ($response_status_code->item(0)->nodeValue != '1') {
    		$this->_error = true;
    		$this->_error_messages[] = $response->getElementsByTagName('Error')->item(0)->getElementsByTagName('ErrorCode')->item(0)->nodeValue . ': ' . $response->getElementsByTagName('Error')->item(0)->getElementsByTagName('ErrorDescription')->item(0)->nodeValue;
    	}else {
    		$rated_shipments = $rating_service_selection_response->getElementsByTagName('RatedShipment');
    		
    		foreach ($rated_shipments as $rated_shipment) {
    			$service = $rated_shipment->getElementsByTagName('Service')->item(0);
    			$code = $service->getElementsByTagName('Code')->item(0)->nodeValue;
    			$total_charges = $rated_shipment->getElementsByTagName('TotalCharges')->item(0);
    			$cost = $total_charges->getElementsByTagName('MonetaryValue')->item(0)->nodeValue;
    			$currency = $total_charges->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;
    			
    			if ( ! ($code && $cost)) {
    				continue;
    			}
    			
    			//convert currency
    			if ($currency != $osC_Currencies->getCode()) {
    				$cost = $this->_convert($cost, $currency, $osC_Currencies->getCode());
    			}
    			
    			//check wether it is allowed method for the selected origin
    			if (in_array($code, $allowed_codes)) {
    				
    				//get title
    				$title = '';
    				foreach ($this->service_code[MODULE_SHIPPING_UPS_ORIGIN] as $service) {
    					if ($service['id'] == $code) {
    						$title = $service['text'];
    						break;
    					}
    				}
    				
    				$quotes['methods'][] = array(
							'id' => $this->_code . $code,
							'title' => $title,
							'cost' => $cost
						);              
    			}
    		}
    	}
    	
    	return $quotes;
    	
    }
    
    /**
     * Build the api request for getting the real-time quote
     *
     * @acess private
     * @return  xml
     */
    function _build_qoute_xml() {
    	global $osC_ShoppingCart, $osC_Weight, $osC_Length, $osC_Currencies, $osC_Database, $osC_Language;
    	
    	//get the shipping address
    	$shipping_address = $osC_ShoppingCart->getShippingAddress();
    	
    	//get the sub total
    	$sub_total = $osC_ShoppingCart->getSubTotal();
    	
    	//verify whether the default shipping weight unit is pounds. Otherwise, it is necessary to convert it to pounds
    	$weight = $osC_ShoppingCart->getWeight();
    	if (SHIPPING_WEIGHT_UNIT != MODULE_SHIPPING_UPS_WEIGHT_CLASS_ID) {
    		$weight = $osC_Weight->convert($osC_ShoppingCart->getWeight(), SHIPPING_WEIGHT_UNIT, MODULE_SHIPPING_UPS_WEIGHT_CLASS_ID);
    	}
    	 
    	//adjust weight key
    	$Qweight = $osC_Database->query('select weight_class_key from :table_weight_class where weight_class_id = :weight_class_id and language_id = :language_id');
    	$Qweight->bindTable(':table_weight_class', TABLE_WEIGHT_CLASS);
    	$Qweight->bindInt(':weight_class_id', MODULE_SHIPPING_UPS_WEIGHT_CLASS_ID);
    	$Qweight->bindInt(':language_id', $osC_Language->getID());
    	$Qweight->execute();
    	 
    	$weight_key = strtoupper($Qweight->value('weight_class_key'));
    	
    	$Qweight->freeResult();
    	
    	if ($weight_key == 'KG') {
    		$weight_key = 'KGS';
    	} elseif ($weight_key == 'LB') {
    		$weight_key = 'LBS';
    	}
    		
    	$weight = ($weight < 0.1 ? 0.1 : $weight);
    	
    	//dimensions
    	$length = $osC_Length->convert(MODULE_SHIPPING_UPS_DIMENSIONS_LENGTH, SHIPPING_LENGTH_UNIT, MODULE_SHIPPING_UPS_LENGTH_CLASS_ID);
    	$width = $osC_Length->convert(MODULE_SHIPPING_UPS_DIMENSIONS_WIDTH, SHIPPING_LENGTH_UNIT, MODULE_SHIPPING_UPS_LENGTH_CLASS_ID);
    	$height = $osC_Length->convert(MODULE_SHIPPING_UPS_DIMENSIONS_HEIGHT, SHIPPING_LENGTH_UNIT, MODULE_SHIPPING_UPS_LENGTH_CLASS_ID);
    	
    	//length key
    	$length_key = strtoupper($osC_Length->getKey(MODULE_SHIPPING_UPS_LENGTH_CLASS_ID));
    	
    	//build xml data for the request
    	$xml = '<?xml version="1.0"?>';
    	$xml .= '<AccessRequest xml:lang="en-US">';
    	$xml .= '	<AccessLicenseNumber>' . MODULE_SHIPPING_UPS_ACCESS_KEY . '</AccessLicenseNumber>';
    	$xml .= '	<UserId>' . MODULE_SHIPPING_UPS_USRERNAME . '</UserId>';
    	$xml .= '	<Password>' . MODULE_SHIPPING_UPS_PASSWORD . '</Password>';
    	$xml .= '</AccessRequest>';
    	$xml .= '<?xml version="1.0"?>';
    	$xml .= '<RatingServiceSelectionRequest xml:lang="en-US">';
    	$xml .= '	<Request>';
    	$xml .= '		<TransactionReference>';
    	$xml .= '			<CustomerContext>Bare Bones Rate Request</CustomerContext>';
    	$xml .= '			<XpciVersion>1.0001</XpciVersion>';
    	$xml .= '		</TransactionReference>';
    	$xml .= '		<RequestAction>Rate</RequestAction>';
    	$xml .= '		<RequestOption>shop</RequestOption>';
    	$xml .= '	</Request>';
    	$xml .= '   <PickupType>';
    	$xml .= '       <Code>' . MODULE_SHIPPING_UPS_PICKUP . '</Code>';
    	$xml .= '   </PickupType>';
    	
    	if (MODULE_SHIPPING_UPS_COUNTRY == 'US' && MODULE_SHIPPING_UPS_PICKUP == '11') {
    		$xml .= '   <CustomerClassification>';
    		$xml .= '       <Code>' . MODULE_SHIPPING_UPS_CLASSIFICATION . '</Code>';
    		$xml .= '   </CustomerClassification>';
    	}
    	
    	$xml .= '	<Shipment>';
    	$xml .= '		<Shipper>';
    	$xml .= '			<Address>';
    	$xml .= '				<City>' . MODULE_SHIPPING_UPS_CITY . '</City>';
    	$xml .= '				<StateProvinceCode>'. MODULE_SHIPPING_UPS_STATE . '</StateProvinceCode>';
    	$xml .= '				<CountryCode>' . MODULE_SHIPPING_UPS_COUNTRY . '</CountryCode>';
    	$xml .= '				<PostalCode>' . MODULE_SHIPPING_UPS_POSTCODE . '</PostalCode>';
    	$xml .= '			</Address>';
    	$xml .= '		</Shipper>';
    	$xml .= '		<ShipTo>';
    	$xml .= '			<Address>';
    	$xml .= ' 				<City>' . $shipping_address['city'] . '</City>';
    	$xml .= '				<StateProvinceCode>' . $shipping_address['zone_code'] . '</StateProvinceCode>';
    	$xml .= '				<CountryCode>' . $shipping_address['country_iso_code_2'] . '</CountryCode>';
    	$xml .= '				<PostalCode>' . $shipping_address['postcode'] . '</PostalCode>';
    	
    	if (MODULE_SHIPPING_UPS_QUOTE_TYPE == 'residential') {
    		$xml .= '				<ResidentialAddressIndicator />';
    	}
    	
    	$xml .= '			</Address>';
    	$xml .= '		</ShipTo>';
    	$xml .= '		<ShipFrom>';
    	$xml .= '			<Address>';
      $xml .= '				<City>' . MODULE_SHIPPING_UPS_CITY . '</City>';
    	$xml .= '				<StateProvinceCode>'. MODULE_SHIPPING_UPS_STATE . '</StateProvinceCode>';
    	$xml .= '				<CountryCode>' . MODULE_SHIPPING_UPS_COUNTRY . '</CountryCode>';
    	$xml .= '				<PostalCode>' . MODULE_SHIPPING_UPS_POSTCODE . '</PostalCode>';
    	$xml .= '			</Address>';
    	$xml .= '		</ShipFrom>';
    	
    	$xml .= '		<Package>';
    	$xml .= '			<PackagingType>';
    	$xml .= '				<Code>' . MODULE_SHIPPING_UPS_PACKAGING_TYPE . '</Code>';
    	$xml .= '			</PackagingType>';
    	
    	$xml .= '		  <Dimensions>';
    	$xml .= '				<UnitOfMeasurement>';
    	$xml .= '					<Code>' . $length_key . '</Code>';
    	$xml .= '				</UnitOfMeasurement>';
    	$xml .= '				<Length>' . $length . '</Length>';
    	$xml .= '				<Width>' . $width . '</Width>';
    	$xml .= '				<Height>' . $height . '</Height>';
    	$xml .= '			</Dimensions>';
    	
    	$xml .= '			<PackageWeight>';
    	$xml .= '				<UnitOfMeasurement>';
    	$xml .= '					<Code>' . $weight_key . '</Code>';
    	$xml .= '				</UnitOfMeasurement>';
    	$xml .= '				<Weight>' . $weight . '</Weight>';
    	$xml .= '			</PackageWeight>';
    	
    	if (MODULE_SHIPPING_UPS_ENABLE_INSURANCE == 'Yes') {
    		$xml .= '           <PackageServiceOptions>';
    		$xml .= '               <InsuredValue>';
    		$xml .= '                   <CurrencyCode>' . $osC_Currency->getCode() . '</CurrencyCode>';
    		$xml .= '                   <MonetaryValue>' . $osC_Currency->format($sub_total) . '</MonetaryValue>';
    		$xml .= '               </InsuredValue>';
    		$xml .= '           </PackageServiceOptions>';
    	}
    	
    	$xml .= '		</Package>';
    	$xml .= '	</Shipment>';
    	$xml .= '</RatingServiceSelectionRequest>';
    	
    	return $xml;
    }
    
    /**
     * Convert the currency
     * 
     * @access private
     * @param $cost
     * @param $from
     * @param $to
     * @return int
     */
    function _convert($cost, $from, $to) {
    	global $osC_Currencies;
    	
    	if ($osC_Currencies->exists($from)) {
    		$from = $osC_Currencies->value($from);
    	} else {
    		$from = 0;
    	}
    	
    	if ($osC_Currencies->exists($to)) {
    		$to = $osC_Currencies->value($to);
    	} else {
    		$to = 0;
    	}
    	
    	return $cost * ($to / $from);
    }
    
    /**
     * set the contact message as error happened
     *
     * @acess private
     * @return void
     */
    function _set_contact_message() {
    	global $osC_Language;
    
    	$store_info = explode("\n", STORE_NAME_ADDRESS);
    	$store_telephone = array_pop($store_info);
    	$this->_error_messages[] = '<div style="border:2px dotted red;padding:0 10px;color: red;margin: 5px 0;">' .
    					'<p style="font-size:normal;font-weight:normal;">' . $osC_Language->get('shipping_ups_error') . '</p>' .
    					'<p><strong>' . $store_telephone . '</strong></p>' .
    					'<p><strong>Email: </strong>' . STORE_OWNER_EMAIL_ADDRESS . '</p>' .
    					'</div>';
    }
  }
?>
