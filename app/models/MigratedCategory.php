<?php

class MigratedCategory extends Eloquent {
	public $timestamps    = false;
	protected $fillable   = array('category_id', 'parent_id', 'name_thai', 'slug_thai', 'name_eng', 'slug_eng',   'images');

	protected $table = 'migrated_category';

}