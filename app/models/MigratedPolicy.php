<?php

class MigratedPolicy extends Eloquent {

	protected $fillable = array('policy_id', 'name_thai', 'name_eng', 'type_thai', 'type_eng', 'description_thai', 'description_eng', 'short_desc_thai', 'short_desc_eng', 'logo_thai', 'logo_eng');

	protected $table = 'migrated_policy';

}