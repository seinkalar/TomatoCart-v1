<?php
/*
 $Id: products.php $
TomatoCart Open Source Shopping Cart Solutions
http://www.tomatocart.com

Copyright (c) 2009 Wuxi Elootec Technology Co., Ltd;  Copyright (c) 2006 osCommerce

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License v2 (1991)
as published by the Free Software Foundation.
*/

class osC_Products {
	var $_category,
	$_recursive = true,
	$_manufacturer,
	$_filters = array(),
	$_products_attributes,
	$_sql_query,
	$_sort_by,
	$_sort_by_direction;

	/* Class constructor */

	function osC_Products($id = null) {
		if (is_numeric($id)) {
			$this->_category = $id;
		}

		if ( (defined('DISPLAY_SUBCATALOGS_PRODUCTS')) && ((int)DISPLAY_SUBCATALOGS_PRODUCTS == -1) ) {
			$this->_recursive = false;
		}
	}

	/* Public methods */

	function hasCategory() {
		return isset($this->_category) && !empty($this->_category);
	}

	function isRecursive() {
		return $this->_recursive;
	}

	function hasManufacturer() {
		return isset($this->_manufacturer) && !empty($this->_manufacturer);
	}

	function hasProductAttributes() {
		return isset($this->_products_attributes) && !empty($this->_products_attributes);
	}

	function setCategory($id, $recursive = true) {
		$this->_category = $id;

		if ($recursive === false) {
			$this->_recursive = false;
		}
	}

	function setManufacturer($id) {
		$this->_manufacturer = $id;
	}

	function setFilters($filters) {
		global $osC_Database;
		 
		//get current actived filters
		if (count($filters) > 0) {
			foreach ($filters as $filters_id) {
				$Qfilter_groups = $osC_Database->query('select filters_groups_id from :table_filters where filters_id = :filters_id');
				$Qfilter_groups->bindTable(':table_filters', TABLE_FILTERS);
				$Qfilter_groups->bindInt(':filters_id', $filters_id);
				$Qfilter_groups->execute();
				
				if ($Qfilter_groups->numberOfRows() > 0) {
					$group = $Qfilter_groups->toArray();
					if ( ! isset($this->_filters[$group['filters_groups_id']])) {
						$this->_filters[$group['filters_groups_id']] = array();
					}
				}
				 
				$this->_filters[$group['filters_groups_id']][] = $filters_id;
				 
				$Qfilter_groups->freeResult();
			}
		}
	}

	function setProductAttributesFilter($products_attributes) {
		$this->_products_attributes = $products_attributes;
	}

	function setSortBy($field, $direction = '+') {
		switch ($field) {
			case 'sku':
				$this->_sort_by = 'p.products_sku';
				break;
			case 'manufacturer':
				$this->_sort_by = 'm.manufacturers_name';
				break;
			case 'quantity':
				$this->_sort_by = 'p.products_quantity';
				break;
			case 'weight':
				$this->_sort_by = 'p.products_weight';
				break;
			case 'price':
				$this->_sort_by = 'final_price';
				break;
		}

		$this->_sort_by_direction = ($direction == '-') ? '-' : '+';
	}

	function setSortByDirection($direction) {
		$this->_sort_by_direction = ($direction == '-') ? '-' : '+';
	}

	function getFilterGroups($cache, $count) {
		global $osC_Database, $osC_Language, $current_category_id, $osC_CategoryTree, $osC_Image;
		 
		$filter_groups = array();
		 
		$Qcategory_filters = $osC_Database->query('select ctf.filters_id, f.filters_groups_id, f.sort_order as filter_sort, fd.filters_name, fg.sort_order as group_sort, fgd.filters_groups_name from :table_categories_to_filters ctf inner join :table_filters f on ctf.filters_id = f.filters_id inner join :table_filters_description fd on (f.filters_id = fd.filters_id and fd.language_id = :language_id) inner join :table_filters_groups fg on f.filters_groups_id = fg.filters_groups_id inner join :table_filters_groups_description fgd on (f.filters_groups_id = fgd.filters_groups_id and fgd.language_id = :language_id) where ctf.categories_id = :categories_id order by fg.sort_order, f.sort_order');
		$Qcategory_filters->bindTable(':table_categories_to_filters', TABLE_CATEGORIES_TO_FILTERS);
		$Qcategory_filters->bindTable(':table_filters', TABLE_FILTERS);
		$Qcategory_filters->bindTable(':table_filters_description', TABLE_FILTERS_DESCRIPTION);
		$Qcategory_filters->bindTable(':table_filters_groups', TABLE_FILTERS_GROUPS);
		$Qcategory_filters->bindTable(':table_filters_groups_description', TABLE_FILTERS_GROUPS_DESCRIPTION);
		$Qcategory_filters->bindInt(':categories_id', $current_category_id);
		$Qcategory_filters->bindInt(':language_id', $osC_Language->getID());
		$Qcategory_filters->bindInt(':language_id', $osC_Language->getID());
		$Qcategory_filters->execute();
		 
		//build the filters groups
		if ($Qcategory_filters->numberOfRows() > 0) {
			while($Qcategory_filters->next()) {
				//verify whether the groups is pushed
				if ( ! isset($filter_groups[$Qcategory_filters->valueInt('filters_groups_id')])) {
					$filter_groups[$Qcategory_filters->valueInt('filters_groups_id')] = array(
						'group_name' => $Qcategory_filters->value('filters_groups_name'),
						'group_sort' => $Qcategory_filters->value('group_sort'),
						'filters' => array()
					);
				}
				
				$filters_name = $Qcategory_filters->value('filters_name');
				
				if ($count == 'Yes') {
					$filters_name .= '(' . $this->calculateProductsCount($Qcategory_filters->valueInt('filters_id')) . ')';
				}
				 
				$filter_groups[$Qcategory_filters->valueInt('filters_groups_id')]['filters'][] = array(
					'filters_id' => $Qcategory_filters->valueInt('filters_id'),
					'filters_name' => $filters_name,
					'filter_sort' => $Qcategory_filters->valueInt('filter_sort')
				);
			}
		}
		 
		$Qcategory_filters->freeResult();
		 
		return $filter_groups;
	}

	function calculateProductsCount($filters_id) {
		global $osC_Database, $osC_Language, $osC_CategoryTree, $osC_Image;
		
		$Qfilter_group = $osC_Database->query('select filters_groups_id from :table_filters where filters_id = :filters_id');
		$Qfilter_group->bindTable(':table_filters', TABLE_FILTERS);
		$Qfilter_group->bindInt(':filters_id', $filters_id);
		$Qfilter_group->execute();
		
		$filter_group = $Qfilter_group->toArray();
		$filter_group_id = $filter_group['filters_groups_id'];
		
		$Qfilter_group->freeResult();

		//find the current actived filter groups
		$actived_filter_groups = array();
		foreach ($this->_filters as $group_id => $filters) {
			if ($group_id != $filter_group_id) {
				$actived_filter_groups[$group_id] = $filters;
			}
		}

		//calculate
		$Qlisting = $osC_Database->query('select count(*) as total from :table_products p left join :table_products_variants pv on (p.products_id = pv.products_id and pv.is_default = 1) left join :table_manufacturers m using(manufacturers_id) left join :table_specials s on (p.products_id = s.products_id) left join :table_manufacturers_info mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = :languages_id) left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag)');
		$Qlisting->bindTable(':table_products', TABLE_PRODUCTS);
		$Qlisting->bindTable(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
		$Qlisting->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
		$Qlisting->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
		$Qlisting->bindTable(':table_specials', TABLE_SPECIALS);
		$Qlisting->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);

		$Qlisting->appendQuery(' inner join :table_products_to_filters ptf on p.products_id = ptf.products_id');
		$Qlisting->bindTable(':table_products_to_filters', TABLE_PRODUCTS_TO_FILTERS);

		if ($this->hasCategory()) {
			$Qlisting->appendQuery(', :table_products_description pd, :table_categories c, :table_products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id');
			$Qlisting->bindTable(':table_categories', TABLE_CATEGORIES);
			$Qlisting->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
			$Qlisting->appendQuery('and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id');
		}else {
			$Qlisting->appendQuery(', :table_products_description pd where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id');
		}

		$Qlisting->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
		$Qlisting->bindInt(':default_flag', 1);
		$Qlisting->bindInt(':language_id', $osC_Language->getID());
		$Qlisting->bindInt(':languages_id', $osC_Language->getID());

		$Qlisting->appendQuery('and ptf.filters_id = :filters_id');
		$Qlisting->bindInt(':filters_id', $filters_id);

		if ($this->hasCategory()) {
			if ($this->isRecursive()) {
				$subcategories_array = array($this->_category);

				$Qlisting->appendQuery('and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and p2c.categories_id in (:categories_id)');
				$Qlisting->bindRaw(':categories_id', implode(',', $osC_CategoryTree->getChildren($this->_category, $subcategories_array)));
			} else {
				$Qlisting->appendQuery('and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = :language_id and p2c.categories_id = :categories_id');
				$Qlisting->bindInt(':language_id', $osC_Language->getID());
				$Qlisting->bindInt(':categories_id', $this->_category);
			}
		}

		if ($this->hasManufacturer()) {
			$Qlisting->appendQuery('and m.manufacturers_id = :manufacturers_id');
			$Qlisting->bindInt(':manufacturers_id', $this->_manufacturer);
		}

		$products = array();
		if ($this->hasProductAttributes()) {
			foreach ($this->_products_attributes as $products_attributes_values_id => $value) {
				if( !empty($value) ){
					$Qproducts = $osC_Database->query('select products_id from :table_products_attributes where products_attributes_values_id = :products_attributes_values_id and value = :value and language_id = :language_id');
					$Qproducts->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
					$Qproducts->bindInt(':products_attributes_values_id', $products_attributes_values_id);
					$Qproducts->bindValue(':value', $value);
					$Qproducts->bindInt(':language_id', $osC_Language->getID());
					$Qproducts->execute();


					$tmp_products = array();
					while ($Qproducts->next()) {
						$tmp_products[] = $Qproducts->valueInt('products_id');
					}
					$products[] = $tmp_products;

					$Qproducts->freeResult();
				}
			}

			if (!empty($products)) {
				$products_ids = $products[0];

				for($i = 1; $i < sizeof($products); $i++) {
					$products_ids = array_intersect($products_ids, $products[$i]);
				}
			}
			
			if ( !empty($products_ids) ) {
				$Qlisting->appendQuery('and p.products_id in (' . implode(',', $products_ids) . ' ) ');
			} else {
				//if no products match, then do not display any result
				$Qlisting->appendQuery('and 1 = 0 ');
			}
		}
		
		//deal with filters
		$filtered_products = array();
		if (count($actived_filter_groups) > 0) {
			$i = 0;
			foreach ($actived_filter_groups as $group_id => $filters) {
				$Qfilter_products = $osC_Database->query('select products_id from :table_products_to_filters where filters_id in (:filters_id)');
				$Qfilter_products->bindTable(':table_products_to_filters', TABLE_PRODUCTS_TO_FILTERS);
				$Qfilter_products->bindRaw(':filters_id', implode(',', $filters));
			
				$Qfilter_products->execute();
			
				$temp_products = array();
				if ($Qfilter_products->numberOfRows() > 0) {
					while ($Qfilter_products->next()) {
						$temp_products[] = $Qfilter_products->valueInt('products_id');
					}
				}
			
				$Qfilter_products->freeResult();
			
				if ($i == 0) {
					$filtered_products = $temp_products;
				}else {
					$filtered_products = array_intersect($filtered_products, $temp_products);
				}
			
				$i++;
			}
			
			$Qfilter_products->freeResult();
			
			if (count($filtered_products) > 0) {
				$Qlisting->appendQuery('and p.products_id in (' . implode(',', $filtered_products) . ')');
			}else {
				$Qlisting->appendQuery('and 1=0');
			}
		}
		
		$Qlisting->execute();
		
		if ($Qlisting->numberOfROws() == 1) {
			$count_info = $Qlisting->toArray();
			 
			return $count_info['total'];
		}

		return 0;
	}

	function &execute() {
		global $osC_Database, $osC_Language, $osC_CategoryTree, $osC_Image;

		$Qlisting = $osC_Database->query('select p.*, pd.*, m.*, if(s.status, s.specials_new_products_price, null) as specials_new_products_price, if(s.status, s.specials_new_products_price, if (pv.products_price, pv.products_price, p.products_price)) as final_price, i.image from :table_products p left join :table_products_variants pv on (p.products_id = pv.products_id and pv.is_default = 1) left join :table_manufacturers m using(manufacturers_id) left join :table_specials s on (p.products_id = s.products_id) left join :table_manufacturers_info mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = :languages_id) left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag)');
		$Qlisting->bindTable(':table_products', TABLE_PRODUCTS);
		$Qlisting->bindTable(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
		$Qlisting->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
		$Qlisting->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
		$Qlisting->bindTable(':table_specials', TABLE_SPECIALS);
		$Qlisting->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);

		if ($this->hasCategory()) {
			$Qlisting->appendQuery(', :table_products_description pd, :table_categories c, :table_products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id');
			$Qlisting->bindTable(':table_categories', TABLE_CATEGORIES);
			$Qlisting->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
			$Qlisting->appendQuery('and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id');
		}else {
			$Qlisting->appendQuery(', :table_products_description pd where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id');
		}

		$Qlisting->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
		$Qlisting->bindInt(':default_flag', 1);
		$Qlisting->bindInt(':language_id', $osC_Language->getID());
		$Qlisting->bindInt(':languages_id', $osC_Language->getID());

		if ($this->hasCategory()) {
			if ($this->isRecursive()) {
				$subcategories_array = array($this->_category);

				$Qlisting->appendQuery('and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and p2c.categories_id in (:categories_id)');
				$Qlisting->bindRaw(':categories_id', implode(',', $osC_CategoryTree->getChildren($this->_category, $subcategories_array)));
			} else {
				$Qlisting->appendQuery('and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = :language_id and p2c.categories_id = :categories_id');
				$Qlisting->bindInt(':language_id', $osC_Language->getID());
				$Qlisting->bindInt(':categories_id', $this->_category);
			}
		}

		if ($this->hasManufacturer()) {
			$Qlisting->appendQuery('and m.manufacturers_id = :manufacturers_id');
			$Qlisting->bindInt(':manufacturers_id', $this->_manufacturer);
		}

		$products = array();
		if ($this->hasProductAttributes()) {
			foreach ($this->_products_attributes as $products_attributes_values_id => $value) {
				if( !empty($value) ){
					$Qproducts = $osC_Database->query('select products_id from :table_products_attributes where products_attributes_values_id = :products_attributes_values_id and value = :value and language_id = :language_id');
					$Qproducts->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
					$Qproducts->bindInt(':products_attributes_values_id', $products_attributes_values_id);
					$Qproducts->bindValue(':value', $value);
					$Qproducts->bindInt(':language_id', $osC_Language->getID());
					$Qproducts->execute();


					$tmp_products = array();
					while ($Qproducts->next()) {
						$tmp_products[] = $Qproducts->valueInt('products_id');
					}
					$products[] = $tmp_products;

					$Qproducts->freeResult();
				}
			}

			if (!empty($products)) {
				$products_ids = $products[0];

				for($i = 1; $i < sizeof($products); $i++) {
					$products_ids = array_intersect($products_ids, $products[$i]);
				}

				if ( !empty($products_ids) ) {
					$Qlisting->appendQuery('and p.products_id in (' . implode(',', $products_ids) . ' ) ');
				} else {
					//if no products match, then do not display any result
					$Qlisting->appendQuery('and 1 = 0 ');
				}
			}
		}
		
		//deal with filters
		if (count($this->_filters) > 0) {
			$filtered_products = array();
			
			$i = 0;
			foreach ($this->_filters as $group_id => $filters) {
				$Qfilter_products = $osC_Database->query('select products_id from :table_products_to_filters where filters_id in (:filters_id)');
				$Qfilter_products->bindTable(':table_products_to_filters', TABLE_PRODUCTS_TO_FILTERS);
				$Qfilter_products->bindRaw(':filters_id', implode(',', $filters));
				
				$Qfilter_products->execute();
				
				$temp_products = array();
				if ($Qfilter_products->numberOfRows() > 0) {
					while ($Qfilter_products->next()) {
						$temp_products[] = $Qfilter_products->valueInt('products_id');
					}
				}
				
				$Qfilter_products->freeResult();
				
				if ($i == 0) {
					$filtered_products = $temp_products;
				}else {
					$filtered_products = array_intersect($filtered_products, $temp_products);
				}
				
				$i++;
			}
			
			if (count($filtered_products) > 0) {
				$Qlisting->appendQuery('and p.products_id in (' . implode(',', $filtered_products) . ')');
			}else {
				$Qlisting->appendQuery('and 1=0');
			}
		}
		
		$Qlisting->appendQuery('order by');

		if (isset($this->_sort_by)) {
			$Qlisting->appendQuery(':order_by :order_by_direction, pd.products_name');
			$Qlisting->bindRaw(':order_by', $this->_sort_by);
			$Qlisting->bindRaw(':order_by_direction', (($this->_sort_by_direction == '-') ? 'desc' : ''));
		} else {
			$Qlisting->appendQuery('pd.products_name :order_by_direction');
			$Qlisting->bindRaw(':order_by_direction', (($this->_sort_by_direction == '-') ? 'desc' : ''));
		}

		$Qlisting->setBatchLimit((isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1), MAX_DISPLAY_SEARCH_RESULTS);
		$Qlisting->execute();
		
		return $Qlisting;
	}
}
?>
