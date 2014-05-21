<?php

class VendorRepository {

	public function buildVendors(Illuminate\Database\Eloquent\Collection $materials)
	{
		// filter materials by vendor_id
		$vendors = array();
		foreach ($materials as $material)
		{
			$idVendor = $material['id_vendor'];
			if (! isset($vendors[$idVendor]))
			{
				$vendors[$idVendor] = array(
					'vendor_id' => $idVendor,
					'shop_id' => $material['shop_id'],
					'master_id' => $material['master_id'],
					'name' => $material['vendor_name'],
					'stock_type' => $material['stock_type'],
				);
			}
		}

		if (count($vendors) > 0)
		{
			$vendorIds = array_keys($vendors);

			// update existing vendors
			$existingVendors = VVendor::whereIn('vendor_id', $vendorIds)->get();
			$existingVendorIds = $existingVendors->lists('vendor_id');
			foreach ($existingVendors as $existingVendor)
			{
				$vendorId = $existingVendor['vendor_id'];
				$existingVendor->shop_id = $vendors[$vendorId]['shop_id'];
				$existingVendor->master_id = $vendors[$vendorId]['master_id'];
				$existingVendor->name = $vendors[$vendorId]['name'];
				$existingVendor->stock_type = $vendors[$vendorId]['stock_type'];
				$existingVendor->save();
			}

        	// insert new vendors
        	$newVendors = array_diff($vendorIds, $existingVendorIds);
        	foreach ($newVendors as $newVendor)
        	{
        		VVendor::insert( $vendors[$newVendor] );
        	}
		}
	}

}