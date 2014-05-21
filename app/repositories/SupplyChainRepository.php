<?php

class SupplyChainRepository implements SupplyChainRepositoryInterface {

	protected $supplychainUrl;

	public function __construct()
	{
		$this->supplychainUrl = Config::get('supplychain.url');
	}

	protected function getLastDailySync()
	{
		return Cron::getValue('last-dailysync', '2014-03-01 00:00:00');
	}
	protected function setLastDailySync( $timestamp )
	{
		Cron::setValue('last-dailysync', $timestamp);
	}

	public function curlGet( $url )
	{
		$data = false;

		try {
			$curlResource = curl_init();
			curl_setopt($curlResource, CURLOPT_URL, $url);
			curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($curlResource);
			curl_close($curlResource);
		} catch (Exception $e) {
			return false;
		}

		return $data;
	}

	public function importNewMaterials($supplychainMaterialData)
	{
		// extract json to table imported_materials
		if($supplychainMaterialData!=false)
		{
			$importedMaterials = new Illuminate\Database\Eloquent\Collection;
			$materials = array();

			// check existing variant (in case that we migrated itruemart before supplychain)
			$inventoryIds = array_pluck($supplychainMaterialData, 'sku_true');
			$exitingVariants = ProductVariant::whereIn('inventory_id', $inventoryIds)->get()->lists('id', 'inventory_id');

			foreach ($supplychainMaterialData as $key => $value)
			{
				$inventoryId = $value['sku_true'];

				$newMaterial = array(
					'variant_id' => array_get($exitingVariants, $inventoryId, null),
					'master_id' => $value['master_id'],
					'shop_id' => $value['shop_id'],
					'id_vendor' => $value['id_vendor'],
					'vendor_name' => $value['full_name'],
					'material_code' => $value['material_code'],
					'inventory_id' => $inventoryId,
					'sku_vendor' => $value['sku_vendor'],
					'stock_type' => $value['stock_type'],
					'status' => $value['status'],
					// 'remark' => $value['remark'],
					'name' => $value['name'],
					'detail' => $value['detail'],
					'plant' => $value['plant'],
					'linesheet' => $value['linesheet'],
					'color' => $value['color'],
					'size' => $value['size'],
					'brand' => $value['brand'],
					'gen' => $value['gen'],
					'surface' => $value['surface'],
					// 'material_description' => $value['material_description'],
					'unit_type' => $value['unit_type'],
					'image_preview_1' => $value['image_preview_1'],
					'image_preview_2' => $value['image_preview_2'],
					'image_preview_3' => $value['image_preview_3'],
					'image_production_1' => $value['image_production_1'],
					'image_production_2' => $value['image_production_2'],
					'image_production_3' => $value['image_production_3'],
					'price' => $value['price'],
					'price_inc_vat' => $value['price_inc_vat'],
					'cost_rtp' => $value['cost_rtp'],
                    'normal_price' => $value['normal_price'],
					// 'is_sourcing_select' => $value['is_sourcing_select'],
					'select_time' => $value['select_time'],
					'sc_create_time' => $value['create_at'],
					// 'is_sap' => $value['is_sap'],
					// 'group_type' => $value['group_type'],
					// 'stock_safety_type' => $value['stock_safety_type'],
					// 'movement_at' => $value['movement_at']
				);

				$materials[$inventoryId] = $newMaterial;
				$importedMaterials->add($newMaterial);
			}

			$materialIds = array_keys($materials);
			$nonVendorVariants = ProductVariant::where('vendor_id', 0)->orWhere('master_id', 0)->orWhereNull('vendor_id')->orWhereNull('master_id')->get();
			if (count($materialIds) > 0)
			{
				$existingMaterials = ImportedMaterial::whereIn('inventory_id', $materialIds)->get();
				$existingMaterialIds = $existingMaterials->lists('inventory_id');

				// update existing materials
				foreach ($existingMaterials as $existingMaterial)
				{
					$existingMaterial->fill($materials[$existingMaterial->inventory_id]);
					$existingMaterial->save();
				}

				// insert new materials
				$newMaterialIds = array_diff($materialIds, $existingMaterialIds);
				foreach ($newMaterialIds as $newMaterialId)
				{
					ImportedMaterial::insert($materials[$newMaterialId]);
				}

				$this->buildVendorsAndShops($importedMaterials);

				// update non-vendor variants
				if (count($nonVendorVariants) > 0)
				{
					$variants = $nonVendorVariants->filter(function($variant) use ($materials)
					{
						if (isset($materials[$variant->inventory_id]))
						{
							return $variant;
						}
					});

					if (count($variants) > 0)
					{
						foreach ($variants as $nonVendorVariant)
						{
							$nonVendorVariant->vendor_id = $materials[$nonVendorVariant->inventory_id]['id_vendor'];
							$nonVendorVariant->master_id = $materials[$nonVendorVariant->inventory_id]['master_id'];
							$nonVendorVariant->save();

							// d('Updated: ', $nonVendorVariant->vendor_id, $nonVendorVariant->master_id);
						}
					}
				}
			}
		}
	}

	protected function buildVendorsAndShops(Illuminate\Database\Eloquent\Collection $materials)
	{
        if($materials->count() > 0)
        {
        	// force update vendors
        	$vendorRepo = new VendorRepository;
        	$vendorRepo->buildVendors($materials);

            // insert new shops by shop_id
            $shopRepo = new ShopRepository;
            $shopRepo->buildShops($materials);
        }
	}

	/**
	 * Cron update new material, daily
	 * @param  [string] $startDate
	 * @param  [string] $endDate
	 * @return [bool] whether the parameter is right or wrong
	 */
	public function dailySync($startDate=null, $endDate=null)
	{
		//ini_set('memory_limit', '16M');
        if( $startDate==false || $endDate==false )
        {
            $lastDailySync = $this->getLastDailySync();

            $startDate = date('Y-m-d H:i:s', strtotime( $lastDailySync.' +1 sec' ));
            $endDate = date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'));
        }

		if( $startDate > $endDate )
      {
          throw new Exception('Start date must less than End date');
      }

		// $json = curl http://esourcing.igetapp.com/api_v2/get_new_material?start_date=2013-10-01 00:00:00&end_date=2013-10-01 23:59:59
      $supplyChainUrl = $this->supplychainUrl.'/api_v2/get_new_material?'.http_build_query(array('start_date'=>$startDate, 'end_date'=>$endDate));

		$data = $this->curlGet( $supplyChainUrl );
		$obj = json_decode($data, true);

		$allData = array_get( $obj, 'jsonData.data.materials' );
      $allDataCount = count($allData);

		$this->importNewMaterials($allData);

		$this->setLastDailySync( $endDate );

		// rebuild table vendors only updated vendor_id

		return $allDataCount;
	}

	protected function getLastSyncLot()
	{
		return Cron::getValue('last-synclot', '2013-10-01 00:00:00');
	}
	protected function setLastSyncLot( $timestamp )
	{
		Cron::setValue('last-synclot', $timestamp);
	}
	protected function flushStockCache()
	{
		$repo = new StockRepository();

		$apps = PApp::all();
		foreach($apps as $app)
		{
			$repo->flush($app->id);
		}
	}

	public function importLots($supplyChainLots)
	{
		if ($supplyChainLots != false)
		{
			foreach($supplyChainLots as $key => $lotData)
	        {
	        	$inventory_id = $lotData['inventory_id'];

	    		foreach($lotData['lots'] as $lotNo => $lotData)
	    		{
	    			$lot = VariantLot::where('inventory_id', $inventory_id)->where('lot_no', $lotNo)->first();

    				if($lot != false)
    				{
    					$lot->cost = floatval($lotData['cost']);
    					$lot->quantity = intval($lotData['qty']);
    					$lot->save();
    				}
    				else
    				{
	        			$lot = new VariantLot();
	        			$lot->inventory_id = $inventory_id;
	        			$lot->lot_no = $lotNo;
						$lot->cost = floatval($lotData['cost']);
	        			$lot->quantity = intval($lotData['qty']);
	        			$lot->save();
    				}
	        	}
	        }
		}
	}

	public function syncLot($startDate, $endDate)
	{
        if( $startDate==false )
        {
            $lastSyncLot = $this->getLastSyncLot();
            $startDate = date('Y-m-d H:i:s', strtotime( $lastSyncLot.' +1 sec' ));
        }
        if( $endDate==false )
        {
            $endDate = date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'));
        }

		if( $startDate > $endDate )
			return false;

        $supplyChainUrl = $this->supplychainUrl.'/api_v2/get_new_lot?'.http_build_query(array('start_date'=>$startDate, 'end_date'=>$endDate));

		$data = $this->curlGet( $supplyChainUrl );
		$obj = json_decode($data, true);


		$allInventories = array_get( $obj, 'jsonData.data.materials' );
        $allInventoryCount = count($allInventories);

        if ($allInventories == false)
        	return false;

        $this->importLots($allInventories);

        $this->setLastSyncLot( $endDate );

        $this->flushStockCache();

	}

}