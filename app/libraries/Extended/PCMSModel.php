<?php namespace Extended;

use Config;
use Teepluss\Harvey\Harvey;
use PCMSKey;
use VerbUtil;
use Illuminate\Support\Facades\DB;

class PCMSModel extends Harvey {

	public static $autoKey = true;
	protected $softDelete = true;

	/**
	 * Error messages.
	 * @var object
	 */
	protected $errors;

	/**
	 * Translated field for update to database later.
	 * @var array
	 */
	protected $dictionaries = array();

	/**
	 * Field that want to translate
	 * @var array
	 */
	protected $translateFields = array();

	/**
	 * Translated string
	 * @var array
	 */
	protected $translated = array();

	/**
	 * Validation rules.
	 *
	 * @var array
	 */
	public static $rules = array();

	/**
	 * Label text for field
	 */
	public static $labels = array();

	// public static $rules = array(
	//     array('title', 'required', 'on' => 'creating'),
	//     array('description', 'required', 'on' => 'updating'),
	//     array('title', 'required')
	// );

	/**
	 * Validation messages.
	 *
	 * @var array
	 */
	public static $messages = array();

	/**
	 * for SoftBelongsToMany
	 */
	protected $softBelongsToMany = array();

	public static function createKey($vid)
	{
		$model = new PCMSKey;
		$model->vid = $vid;

		$attemp = 200;
		do
		{
			//$model->code = $vid.rand(10,99).floor(time()/1000).rand(100,999);
			$model->code = $vid.rand(10,99).rand(100,999).rand(10,99).rand(10,99).rand(100,999);
			$attemp--;
		}
		while($model->save()==false && $attemp>0);

		if($attemp>0)
			return $model->code;

		return false;
	}

	// public function pcmsKey()
	// {
	//     // get vid from verb
	//     $vid = VerbUtil::getVid($model->getTable());

	//     return $this->belongsTo('PCMSKey', 'pkey')->whereVid($vid);
	// }

	public static function boot()
	{
		parent::boot();

		if(static::$autoKey!=false)
		{
			static::creating(function($model)
			{
				$model->pkey = $model::createKey(VerbUtil::getVid($model->getTable()));
			});

			static::updating(function($model)
			{
				if($model->pkey==false)
					$model->pkey = $model::createKey(VerbUtil::getVid($model->getTable()));
			});
		}

		static::saved(function($model)
		{
			$dictionaries = $model->getDictionaries();

			// insert translation to database
			if(count($dictionaries) > 0) {

				// query translate record
				$translated = $model->translates()->get();

				$configLocale = Config::get('locale');

				foreach($configLocale as $locale => $lang)
				{
					// find in database with locale
					$filtered = $translated->filter(function($item) use ($locale) {
						if($item->locale == $locale)
							return $item;
					});

					if(count($filtered) < 1) {
						// not found record - it's new
						if(isset($dictionaries[$locale])){
							// has dictionary so create it
							$dictionary = $dictionaries[$locale];
							// assign locale to dictionary
							$dictionary['locale'] = $locale;
							// create it.
							$model->translates()->create($dictionary);
						}
					} else {
						// found record
						$translate_record = $filtered->first();

						if(isset($dictionaries[$locale])){
							// found dictionary - update dictionary to database
							$dictionary = $dictionaries[$locale];
							foreach($dictionary as $field => $string)
							{
								$translate_record->{$field} = $string;
							}
							// get array from model
							$translate_array = $translate_record->toArray();
							// config column that want skip
							$except_columns = array('id', 'locale', 'languagable_id', 'languagable_type');
							// check any translation that removed or empty - remove it
							foreach($translate_array as $field => $string)
							{
								// skip column that about polymorphic
								if(in_array($field, $except_columns)) {
									continue;
								}
								// set field that want remove value
								if(empty($dictionary[$field]))
								{
									if(isset($translate_record->{$field})) {
										$translate_record->{$field} = "";
									}
								}
							}
							// update it.
							$translate_record->save();
						} else {
							// not found dictionary - user want to delete
							$translate_record->delete();
						}
					}
				}
				// set blank - don't know if it will work or not but safety first
				$model->clearDictionaries();
			}

		});

/*
		$rules = static::$rules;
		$messages = static::$messages;

		foreach (array('creating', 'updating') as $event)
		{
			static::registerModelEvent($event, function($model) use ($event, $rules, $messages)
			{
				$attributes = $model->getAttributes();

				$dirty = $model->getDirty();

				$v = array();

				foreach ($rules as $key => $rule)
				{
					$field = array_shift($rule);

					if ( ! isset($rule['on']) or $rule['on'] == $event)
					{
						unset($rule['on']);

						if (array_key_exists($field, $v))
						{
							$rule = array_merge($v[$field], $rule);

							$rule = array_unique($rule);
						}

						if (!isset($dirty[$field])) continue;

						$v[$field] = $rule;
					}
				}

				$validator = Validator::make($attributes, $v, $messages);

				if ($validator->fails())
				{
					$model->errors = $validator->messages();

					return false;
				}
			});
		}
*/
	}

	/**
	 * Extend validate from Harvey
	 *
	 * @param  array  $rules
	 * @param  array  $messages
	 * @param  array  $inputs
	 * @return boolean
	 */
	public function validate(array $rules, array $messages = array(), array $inputs = array())
	{
		$passed = parent::validate($rules, $messages, $inputs);

		if($this->dictionaries==false) {
			return $passed;
		}

		// validate translate th_TH must required.
		/*
		if(empty($this->dictionaries['th_TH']))
		{
			$passed = false;
			$this->errors->add('all_in_form', 'You must translate to Thai.');
		} else {
			//get english dictionary
			$english_dictionary = $this->dictionaries['th_TH'];

			// check field that have english translate or not.
			foreach ($this->translateFields as $field) {
				// if it doesn't have english, error.
				if(empty($english_dictionary[$field]))
				{
					$passed = false;
					$this->errors->add($field, 'The '.$field.' must have Thai translation.');
				}
			}
		}
		*/

		return $passed;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Polymorphic Relations for Translation
	 * @return object
	 */
	public function translates()
	{
		return $this->morphMany('Translate', 'languagable');
	}

	/**
	 * Translate string
	 * @param  string $locale
	 * @return object
	 */
	public function translate($locale = 'en_US')
	{
		if( ! isset($this->translated[$locale]))
		{
			$translated = $this->translates()->whereLocale($locale)->first();
			// cache to memory
			$this->translated[$locale] = $translated;
		} else {
			// recovery from memory
			$translated = $this->translated[$locale];
		}

		if($translated) {
			$translatedArray = $translated->toArray();
			foreach($translatedArray as $field => $value)
			{
				if(empty($this->{$field})) {
					// should unset field that parent don't have same
					unset($translated->{$field});
				} else {
					if($value == false) {
						// set default replace to translate when blank
						$translated->{$field} = $this->{$field};
					}
				}
			}
		}

		return $translated;
	}

	public static function getLabel($field)
	{
		if(isset(static::$labels[$field]))
			return static::$labels[$field];
		return ucwords(str_replace('_',' ',$field));
	}

	/**
	 * Set translated string to dictionary of model
	 * @param string $field Field name in Translate table
	 * @param array $dictionary
	 */
	public function setTranslate($field, $dictionary)
	{
		// set field to variable for use in validate later
		$this->translateFields[] = $field;

		$configLocale = Config::get('locale');
		if( ! is_array($dictionary) )
			return $this;

		foreach($dictionary as $locale => $string)
		{
			// skip when locale is not define.
			if( ! isset($configLocale[$locale])) continue;

			// skip when string is blank or null
			//if( ! $string && $locale !== 'th_TH' ) continue;
			if( ! $string ) continue;

			// make array of field avaliable
			if( ! isset($this->dictionaries[$locale]))
			{
				$this->dictionaries[$locale] = array();
			}

			// set translated to dictionary
			$this->dictionaries[$locale][$field] = $string;
		}

		return $this;
	}

	public function getDictionaries()
	{
		return $this->dictionaries;
	}

	public function clearDictionaries()
	{
		$this->dictionaries = array();
	}

	/**
	 * Polymorphic Relations for Message
	 * @return object
	 */
	public function messages()
	{
		return $this->morphMany('Message', 'messagable');
	}

	/**
	 * Define a many-to-many relationship.
	 *
	 * @param  string  $related
	 * @param  string  $table
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @return \SoftBelongsToMany
	 */
	public function softBelongsToMany($related, $table = null, $foreignKey = null, $otherKey = null)
	{
		$caller = $this->getBelongsToManyCaller();

		// First, we'll need to determine the foreign key and "other key" for the
		// relationship. Once we have determined the keys we'll make the query
		// instances as well as the relationship instances we need for this.
		$foreignKey = $foreignKey ?: $this->getForeignKey();

		$instance = new $related;

		$otherKey = $otherKey ?: $instance->getForeignKey();

		// If no table name was provided, we can guess it by concatenating the two
		// models using underscores in alphabetical order. The two model names
		// are transformed to snake case from their default CamelCase also.
		if (is_null($table))
		{
			$table = $this->joiningTable($related);
		}

		// Now we're ready to create a new query builder for the related model and
		// the relationship instances for the relation. The relations will set
		// appropriate query constraint and entirely manages the hydrations.
		$query = $instance->newQuery();

		return new \SoftBelongsToMany($query, $this, $table, $foreignKey, $otherKey, $caller);
	}

	// public function softBelongsToMany($related, $table, $foreignKey, $otherKey)
	// {
	//     $this->softBelongsToMany[$related] = $this->belongsToMany($related, $table, $foreignKey, $otherKey)->whereNull($table.'.'.static::DELETED_AT);
	//     return $this->softBelongsToMany[$related];
	// }

	// protected function formatSyncList($records)
	// {
	//     $results = array();

	//     if(is_array($records))
	//         foreach ($records as $id => $attributes)
	//         {
	//             if ( ! is_array($attributes))
	//             {
	//                 list($id, $attributes) = array($attributes, array());
	//             }

	//             $results[$id] = $attributes;
	//         }

	//     return $results;
	// }

	// protected function rawSoftAttach($table, $foreignKey, $otherKey, $ids = array(), $touch = true)
	// {
	//     $ids = (array) $ids;

	//     if($ids == false)
	//         return false;

	//     $records = $this->formatSyncList($ids);

	//     $existing = DB::table($table)
	//                     ->select($otherKey, static::DELETED_AT)
	//                     ->where($foreignKey, $this->getKey())
	//                     ->whereIn($otherKey, array_keys($records))
	//                     //->whereNotNull(static::DELETED_AT)
	//                     ->get();
	//     $current = array_pluck( $existing, $otherKey );

	//     $new = array_diff( array_keys($records), $current );
	//     $restore = array_intersect( array_keys($records), $current );

	//     foreach ($new as $id => $attributes)
	//     {
	//         DB::table($table)
	//             ->insert(array(
	//                 $foreignKey => $this->getKey(),
	//                 $otherKey => $attributes,
	//             ));
	//     }

	//     if (count($restore) > 0)
	//     {
	//         DB::table($table)
	//             ->where($foreignKey, $this->getKey())
	//             ->whereIn($otherKey, $restore)
	//             ->update( array(static::DELETED_AT=>NULL) );
	//     }
	// }

	// public function rawSoftDetach($table, $foreignKey, $otherKey, $ids = array(), $touch = true)
	// {
	//     $results = 0;

	//     if (count($ids) > 0)
	//     {
	//         $results = DB::table($table)
	//                     ->where($foreignKey, $this->getKey())
	//                     ->whereIn($otherKey, $ids)
	//                     ->update( array(static::DELETED_AT => DB::raw('NOW()')) );
	//     }

	//     return $results;
	// }

	// public function rawSoftSync($table, $foreignKey, $otherKey, $ids = array())
	// {
	//     $ids = (array) $ids;

	//     // First we need to attach any of the associated models that are not currently
	//     // in this joining table. We'll spin through the given IDs, checking to see
	//     // if they exist in the array of current ones, and if not we will insert.
	//     $current = DB::table($table)
	//                 ->select($otherKey)
	//                 ->where($foreignKey, $this->getKey())
	//                 ->whereNull(static::DELETED_AT)
	//                 ->get();

	//     $records = $this->formatSyncList($ids);

	//     $detach = array_diff( array_pluck($current,$otherKey), array_keys($records) );

	//     if (count($detach) > 0)
	//     {
	//         $this->rawSoftDetach($table, $foreignKey, $otherKey, $detach);
	//     }

	//     $this->rawSoftAttach($table, $foreignKey, $otherKey, $records, false);
	// }

}