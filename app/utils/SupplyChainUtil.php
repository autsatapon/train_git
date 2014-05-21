<?php

class SupplyChainUtil extends Util {
	
	/**
	 *	get all materials from SupplyChain - daily use
	 */
	public static function dailySync()
	{
		
		// $json = curl http://esourcing.igetapp.com/api/dailysync?date=2013-07-17
		
		// extract json to table imported_materials
		// update all imported materials to variants
		
		// notify if needed
		$startDate = Date('Y-m-d 00:00:00');
		$endDate = Date('Y-m-d 23:59:59');

		$supplyChainUrl = "http://esourcing.igetapp.com/apiDailySync/getDailySync?".http_build_query(array('startDate'=>$startDate, 'endDate'=>$endDate));
		
			$curlResource = curl_init();
			curl_setopt($curlResource, CURLOPT_URL, $supplyChainUrl);
			curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, 1); 
			$data = curl_exec($curlResource);
			curl_close($curlResource);			

		    $obj = json_decode($data,true);
			$all_data = $obj['jsonData']['data'][0];

			
			
			foreach ($all_data as $key => $value) {
				$value['inventory_id'] = $value['sku_true'];
				unset($value['sku_true']);
				
				SupplyChainDailySync::create($value);
			}
			
			//********************* Debuging ***********************//
			// $importmat = new SupplyChainDailySync;
			//foreach ($all_data as $key => $value) {
				//unset($value['sku_true']);
			//}
			//echo "http://esourcing.igetapp.com/apiDailySync/getDailySync?startDate=".$startDate."&endDate=".$endDate;die();
			// SupplyChainDailySync::insert($all_data);
			// $importmat->save();
			//***************************************************//
	}
	
	/**
	 * check single stock by inventory_id
	 * @param numeric|Variant
	 * @param bool
	 */
	public static function checkStock($inventory, $update_on_diff=true)
	{
		
	}
	
	/**
	 * check multiple stock by array of inventory_id
	 * @param array
	 * @param bool
	 */
	public static function batchCheckStock($inventories, $update_on_diff=true)
	{
		
	}
	
}