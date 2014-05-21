<?php

class MigratedPolicyVendor extends Eloquent {

	protected $fillable = array('vendor_code', 'shop_id', 'brand_id', 'policy_id');

	protected $table = 'migrated_policy_vendor';

    public $timestamps = false;

}