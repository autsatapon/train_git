<?php
ini_set('memory_limit','1024M');
set_time_limit(0);
//ini_set('display_errors', 1);
class MigrationItruemartController extends Controller{


	public $submit_translate = array();
	public $submit_collection = array();
	public $group_collection = array();

	public function getIndex()
	{
		return 'welcome';
	}

	//migrated brand itruemart.
	public function getBrand()
	{
	   // $brands   =  MigratedBrand::where('status', 'no')->take(10)->get();
		$brands   =  MigratedBrand::where('status', 'no')->get();

		foreach ($brands as $key => $val)
		{
			$itm_brand_id   = $val['brand_id'];
			$name_thai      = $val['name_thai'];
			$history_thai   = $val['history_thai'];
			$slug_eng       = $val['slug_eng'];
			$name_eng       = $val['name_eng'];
			$history_eng    = $val['history_eng'];
			$vdo            = $val['vdo'];
			$logo_banner    = $val['logo_banner'];
			$logo_icon      = $val['logo_icon'];
			$logo_flashsale = $val['logo_flashsale'];

			// Insert into Brand.
			$brand = new Brand;
			$brand->name = $name_thai;
			$brand->description = $history_thai;
			$brand->slug = $slug_eng;

			if ( !$brand->save() )
			{
				echo 'Cannot Insert Brand';
				$lastQuery = end(DB::getQueryLog());
				s($lastQuery);
				die();
			}

			//insert into translates.
			$translate = new Translate;
			$translate->name = $name_eng;
			$translate->description = $history_eng;
			$translate->languagable_id = $brand->id;
			$translate->languagable_type = 'Brand';
			$translate->locale = 'en_US';

			if ( !$translate->save() )
			{
				echo 'Cannot Insert Translate';
				$lastQuery = end(DB::getQueryLog());
				s($lastQuery);
				die();
			}

			//insert into brand map.
			DB::table('brand_maps')->insert(array(
				'itruemart_id' => $itm_brand_id,
				'pcms_id'      => $brand->id
			));

			// insert MetaData.
			if (!empty($vdo))
			{
				$video = new MetaData;
				$video->app_id = 1;
				$video->type = 'link';
				$video->key = 'video';
				$video->value = $vdo;
				$video->metadatable_id = $brand->id;
				$video->metadatable_type = 'Brand';
				if ( !$video->save() )
				{
					echo 'Cannot Insert Video Metadata';
					$lastQuery = end(DB::getQueryLog());
					s($lastQuery);
					die();
				}
			}

			if (!empty($logo_banner))
			{
				$bannerLogo = new MetaData;
				$bannerLogo->app_id = 1;
				$bannerLogo->type = 'file';
				$bannerLogo->key = 'banner-logo';
				$bannerLogo->value = $logo_banner;
				$bannerLogo->metadatable_id = $brand->id;
				$bannerLogo->metadatable_type = 'Brand';
				$bannerLogo->save();
				if ( !$bannerLogo->save() )
				{
					echo 'Cannot Insert Logo Banner Metadata';
					$lastQuery = end(DB::getQueryLog());
					s($lastQuery);
					die();
				}
			}

			if (!empty($logo_icon))
			{
				$bannerIcon = new MetaData;
				$bannerIcon->app_id = 1;
				$bannerIcon->type = 'file';
				$bannerIcon->key = 'banner-icon';
				$bannerIcon->value = $logo_icon;
				$bannerIcon->metadatable_id = $brand->id;
				$bannerIcon->metadatable_type = 'Brand';
				if ( !$bannerIcon->save() )
				{
					echo 'Cannot Insert Logo Icon Metadata';
					$lastQuery = end(DB::getQueryLog());
					s($lastQuery);
					die();
				}
			}

			if (!empty($logo_flashsale))
			{
				$bannerFlashsale = new MetaData;
				$bannerFlashsale->app_id = 1;
				$bannerFlashsale->type = 'file';
				$bannerFlashsale->key = 'banner-flashsale';
				$bannerFlashsale->value = $logo_flashsale;
				$bannerFlashsale->metadatable_id = $brand->id;
				$bannerFlashsale->metadatable_type = 'Brand';
				if ( !$bannerFlashsale->save() )
				{
					echo 'Cannot Insert Logo Flashsale Metadata';
					$lastQuery = end(DB::getQueryLog());
					s($lastQuery);
					die();
				}
			}

			// Update Status
			$val->status = 'yes';
			$val->save();
		}

		sd(DB::getQueryLog());
	}

/*	public function getTrauncatetable()
	{

		$list_table = DB::select(DB::raw('show tables'));

		d($list_table);

	}*/

	public function getUpdateParentCollection()
	{
		$migrate_category   =  MigratedCategory::where('status', 'yes')->get();

		foreach($migrate_category as $key => $val)
		{
			//get pcms_id from category_maps.
			$migrate_category_id    = $val->category_id;
			$migrate_parent_id      = $val->parent_id;

			$category_map           = $this->getTable('category_maps','itruemart_id',$migrate_category_id);

			$map_pcms_id            = $category_map[0]->pcms_id;
			$map_itm_id             = $category_map[0]->itruemart_id;

			if($migrate_parent_id == 0)
			{
				$param  = array('parent_id' => 0);

				$this->updateTable('collections',$map_pcms_id,$param);
				echo 'id:'. $map_pcms_id.' no parent id';
				echo '<hr>';
			}
			else
			{
				echo 'itruemart id: '.$migrate_category_id .' pcms id: '.$map_pcms_id;
				echo '<br>';

				$category_map_parent    = $this->getTable('category_maps','itruemart_id',$migrate_parent_id);

					if(!empty($category_map_parent))
					{
						$pcms_id    = $category_map_parent[0]->pcms_id;
						echo 'itruemart parent id:'. $migrate_parent_id. ' pcms parent id: ' . $pcms_id;
						echo '<br>';
						$param  = array('parent_id' => $pcms_id);

						$this->updateTable('collections',$map_pcms_id,$param);
					}
					echo '<hr>';
			}

		}

	}
	private function updateTable($table_name,$id,$param=array())
	{
		$res_id    = DB::table($table_name)->where('id', $id)->update($param);
		return $res_id;
	}

	private function getTable($table_name,$filed_name,$id)
	{
		$res_id    = DB::table($table_name)->where($filed_name, $id)->get();
		return $res_id;
	}

	public function getCategory()
	{
		//$rawCategory =  DB::table('migrated_category')->where('status','no')->get();
		$category   =  MigratedCategory::where('status', 'no')->get();

		foreach ($category as $key => $value)
		{
			// step 1 create collection.
			$collection_id = $this->createCollection($value->name_thai,$value->slug_eng);
			echo 'step 1 crate collection id' .$collection_id.'<br>';

			// step 2 create collection map
			if(!empty($collection_id))
			{
				$result = $this->createTableMaps($value->category_id,$collection_id,'category_maps');
				echo 'step 2 create collection map  '.$result .'<br>';
			}
			else
			{
				echo 'Cannot Insert collection map';
				$lastQuery = end(DB::getQueryLog());
				s($lastQuery);
				continue;
			}

			// step 3 crate collection translate.
			if(!empty($collection_id))
			{
				$translate_id = $this->createCategoryTranslate($value->name_eng,$collection_id);
				echo 'step 3 crate collection translate' .$translate_id.'<br>';
			}
			else
			{
				echo 'Cannot Insert collection translate';
				$lastQuery = end(DB::getQueryLog());
				s($lastQuery);
				continue;
			}

			// step 4 crate metadata.
			if(!empty($collection_id))
			{
				$metadata_id  = $this->createMetaData('file','banner',$value->images,$collection_id,'Collection');
				echo 'step 4 crate collection metadata' .$metadata_id.'<br>';
			}
			else
			{
				echo 'Cannot Insert collection metadata';
				$lastQuery = end(DB::getQueryLog());
				s($lastQuery);
				continue;
			}

			$value->status = 'yes';
			$value->save();

		}
		sd(DB::getQueryLog());

		exit();
	}

	//create collection.
	private function createCollection($name_thai,$slug_eng)
	{
		$collection = new Collection();

		$collection->name = $name_thai;
		$collection->slug = $slug_eng;
		$collection->is_category = '1';
		$collection->save();

		if(!$collection->save())
		{
			return false;
		}

		return $collection->id;
	}

	//create collection map.
	private function createTableMaps($itruemart_id,$pcms_id,$table_name)
	{
		$result = DB::table($table_name)->insert(array(
				'itruemart_id' => $itruemart_id,
				'pcms_id'      => $pcms_id
			));

		return $result;
	}

	//crate collection translate.
	private function createCategoryTranslate($name_eng,$collection_id)
	{
		$translate = new Translate();

		$translate->name                = $name_eng;
		$translate->languagable_id      = $collection_id;
		$translate->languagable_type    = 'Collection';
		$translate->locale              = 'en_US';

		if(!$translate->save())
		{
			return false;
		}

		return $translate->id;
	}

	//crate collection translate.
	private function createMetaData($type,$key,$value,$id,$metadatable_type)
	{
		$metaData = new MetaData;

		$metaData->app_id           = 1;     // fix itruemart
		$metaData->type             = $type; //'link'
		$metaData->key              = $key;  //'video'
		$metaData->value            = $value;
		$metaData->metadatable_id   = $id;
		$metaData->metadatable_type = $metadatable_type;

		if(!$metaData->save())
		{
			return false;
		}

		return $metaData->id;
	}

	public function getProduct()
	{
		$product = MigratedProduct::where('status_product','no')->take('10')->get();
		$submit_product = array();
		if(!empty($product))
		{
			$brand_id = '';
			foreach ($product as $key => $value) {

				$category_maps = $this->getTable('category_maps','itruemart_id',$value->category_id);
				if(empty($category_maps))
				{
					MigratedProduct::where('product_id',$value->product_id)->update(array('status_product' => 'error'));
					continue ;
				}

				$this->getCollectionAll($category_maps[0]->pcms_id);

				$check_product_maps = DB::table('product_maps')->where('itruemart_id',$value->product_id)->get();
				if(!empty($check_product_maps))
				{
					MigratedProduct::where('product_id',$value->product_id)->update(array('status_product' => 'yes'));
					continue ;
				}

				$brand_id = DB::table('brand_maps')->where('itruemart_id',$value->brand_id)->get();
				$brand_id = $brand_id[0]->pcms_id;

				$installment = array();
				if($value->installment == 'N')
				{
					$installment = array(
											'allow' => false
										);
				}else{
					$installment = array(
											'allow' => true,
											'periods' => $value->installment_period
										);
				}

				$installment = json_encode($installment);

				$tag = '';
				if(!empty($value->tags))
				{
					$tag = str_replace('|', ',', $value->tags);
				}

				$submit_product[$value->product_id] = array(
									'title' => $value->title,
									'brand_id' => $brand_id,
									'product_line' => $value->title,
									'description' => htmlspecialchars($value->description_thai),
									'key_feature' => $value->key_feture_thai,
									'tag' => $tag,
									'installment' => $installment,
									'has_variants' => 1
								);

				$this->submit_translate[$value->product_id] = array(
													'title' => $value->title_eng,
													'key_feature' => $value->key_feture_eng,
													'description' => htmlspecialchars($value->description_eng),
													'locale' => 'en_US',
													'languagable_type' => 'Product'
												);


			}
		}else{
			dd('finish');
		}



		foreach ($submit_product as $key => $value) {
			$this->createProduct($value,$key);
		}


		if(!empty($this->group_collection))
		{
			foreach ($this->group_collection as $key => $value) {
				$datetime = date('Y-m-d H:i:s');
				$this->submit_collection[] = array(
													'collection_id' => $value['id'],
													'product_id' => $value['product_id'],
													'parent_id' => $value['parent_id'],
													'created_at' => $datetime,
													'updated_at' => $datetime
													);
			}
		}



		//d($this->submit_collection);
		foreach ($this->submit_translate as $key => $value) {
			$this->createTranslate($value);
		}

		foreach ($this->submit_collection as $key => $value) {
			$this->createProductCollection($value);
		}


		echo '<meta http-equiv="refresh" content="0;" />';
		//return Redirect::refresh();



	}

	public function getVarient()
	{



	}


	private function getCollectionAll($pcms_id='')
	{

		if(!empty($pcms_id))
		{
			$collections = Collection::where('id',$pcms_id)->get();

			if(!empty($collections))
			{
				$this->group_collection[$collections[0]->id] = array('id' => $collections[0]->id, 'parent_id' => $collections[0]->parent_id);

				if($collections[0]->parent_id != 0)
				{
					$this->getCollectionAll($collections[0]->parent_id);

				} else {

					$this->group_collection[$collections[0]->id] = array('id' => $collections[0]->id, 'parent_id' => $collections[0]->parent_id);
					//return $this->group_collection;
				}
			}
		}
	}

	private function createProduct($param,$itruemart_id)
	{
		// $product_id = Product::insertGetId($param);
		$product = new Product();

		foreach ($param as $key=>$val)
		{
			$product->{$key} = $val;
		}
		$product->save();

		//d($product->id);

		$product_id = $product->id;
		if(!empty($product_id))
		{
			$this->createTableMaps($itruemart_id,$product_id,'product_maps');
			MigratedProduct::where('product_id',$itruemart_id)->update(array('status_product' => 'yes'));

			$this->submit_translate[$itruemart_id]['languagable_id'] = $product_id;

			if(!empty($this->group_collection))
			{
				foreach ($this->group_collection as $key => $value) {
					$this->group_collection[$key]['product_id'] = $product_id;
				}
			}

		}

	}

	private function createTranslate($param)
	{
		$translate = new Translate();

		foreach ($param as $key=>$val)
		{
			$translate->{$key} = $val;
		}

		$translate->save();

		return $translate->id;

	}

	private function createProductCollection($param)
	{
		$product_collection_id = DB::table('product_collections')->insertGetId($param);
		return $product_collection_id;
	}

	public function getBrandExcel()
	{
		//path file.
		$pathFile = "./excel-migrate-itruemart/brand/brand.xlsx";
		$objPHPExcel = PHPExcel_IOFactory::load($pathFile);

		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		unset($sheetData[1]);

		$cmd   = Input::get('cmd');
		if ($cmd == 'run')
		{
			$index = 0 ;
			foreach ($sheetData as $k => $v)
			{

				$brand[$index] = array(
						'brand_id'      => $v['A'],
						'name_thai'     => htmlspecialchars($v['B'],ENT_QUOTES, 'UTF-8'),
						'history_thai'  => htmlspecialchars($v['C'],ENT_QUOTES, 'UTF-8'),
						'slug_thai'     => $v['D'],
						'name_eng'      => htmlspecialchars($v['E'],ENT_QUOTES, 'UTF-8'),
						'history_eng'   => htmlspecialchars($v['F'],ENT_QUOTES, 'UTF-8'),
						'slug_eng'      => htmlspecialchars($v['G'],ENT_QUOTES, 'UTF-8'),
						'vdo'           => $v['H'],
						'logo_banner'   => $v['I'],
						'logo_icon'     => $v['J'],
						'logo_flashsale'=> $v['K']
						);

				$index++;
			}
			MigratedBrand::insert($brand);
			echo '<br> MigratedBrand success';
		}
	}

	public function getCategoryExcel()
	{
		//path file.
		$pathFile = "./excel-migrate-itruemart/category/category.xlsx";
		$objPHPExcel = PHPExcel_IOFactory::load($pathFile);

		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		unset($sheetData[1]);

		$cmd   = Input::get('cmd');
		if ($cmd == 'run')
		{
			$index = 0 ;
			foreach ($sheetData as $k => $v)
			{

				$category[$index] = array(
						'category_id'   => $v['A'],
						'parent_id'     => $v['B'],
						'name_thai'     => $v['C'],
						'slug_thai'     => $v['D'],
						'name_eng'      => htmlspecialchars($v['E'],ENT_QUOTES, 'UTF-8'),
						'slug_eng'      => htmlspecialchars($v['F'],ENT_QUOTES, 'UTF-8'),
						'images'        => $v['G'],
						);

				$index++;
			}
			MigratedCategory::insert($category);
			echo '<br> MigratedCategory success';
		}
	}

	public function getPolicyExcel()
	{
		echo "Add via admin panel";
		/* *\/
		//path file.
		$pathFile = "./excel-migrate-itruemart/policy/policy.xlsx";
		$objPHPExcel = PHPExcel_IOFactory::load($pathFile);
		echo 'policy';
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		unset($sheetData[1]);

		$cmd   = Input::get('cmd');
		if ($cmd == 'run')
		{
			$index = 0 ;
			foreach ($sheetData as $k => $v)
			{

				$policy[$index] = array(
						'policy_id'         => $v['A'],
						'name_thai'         => $v['B'],
						'name_eng'          => $v['C'],
						'type_thai'         => $v['D'],
						'type_eng'          => $v['E'],
						'description_thai'  => htmlspecialchars($v['F'],ENT_QUOTES, 'UTF-8'),
						'description_eng'   => htmlspecialchars($v['G'],ENT_QUOTES, 'UTF-8'),
						'short_desc_thai'   => htmlspecialchars($v['H'],ENT_QUOTES, 'UTF-8'),
						'short_desc_eng'    => htmlspecialchars($v['I'],ENT_QUOTES, 'UTF-8'),
						'logo_thai'         => $v['J'],
						'logo_eng'          => $v['K'],
						);

				$index++;
			}
			MigratedPolicy::insert($policy);
			echo '<br> MigratedPolicy success';
		}
		/* */
	}

	public function getPolicyVendorExcel()
	{

        $pathFile = public_path() . "/20140515-excel-migrate-itruemart/policy_vendor.xlsx";

		$column = array(
		    'A' => 'vendor code',
		    'B' => 'shop id',
		    'C' => 'brand_id',
		    'D' => 'policy_id',
		    'E' => 'type'
		);

		$model = new MigratedPolicyVendor;

        migrateImportExcel($pathFile, $column, $model);


		// MigratedPolicyVendor::truncate();
		// //path file.
		// $pathFile = public_path() . "/20140515-excel-migrate-itruemart/policy_vendor.xlsx";
		// $objPHPExcel = PHPExcel_IOFactory::load($pathFile);

		// $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

		// $column = array(
		//     'A' => 'vendor code',
		//     'B' => 'shop id',
		//     'C' => 'brand_id',
		//     'D' => 'policy_id',
		//     'E' => 'type'
		// );

		// if (! migrateColumnNameChecker($sheetData, $column))
		// {
		//     throw new Exception("First column name isn't match. Please check excel again before migrate.");
		// }

		// $model = new MigratedPolicyVendor;

		// if (! migrateSaveToRawModel($sheetData, $column, $model))
		// {
		//     throw new Exception("Can't save to model. Please review excel and coding.");
		// }

		// unset($sheetData[1]);

		// $cmd   = Input::get('cmd');
		// if ($cmd == 'run')
		// {
		// 	$index = 0 ;
		// 	foreach ($sheetData as $k => $v)
		// 	{
		// 		$policy_vendor[$index] = array(
		// 				'vendor_code'   => $v['A'],
		// 				'shop_id'       => $v['B'],
		// 				'brand_id'      => $v['C'],
		// 				'policy_id'     => $v['D'],
		// 				);

		// 		$index++;
		// 	}
		// 	MigratedPolicyVendor::insert($policy_vendor);
		// 	echo '<br> MigratedPolicyVendor success';
		// }
		// echo '<br> MigratedPolicyVendor success';
	}

	public function getVendorPolicy()
	{
		$cmd   = Input::get('cmd');
		if ($cmd == 'run')
		{
			DB::connection()->disableQueryLog();
			PolicyRelate::truncate();

			$sql =<<<SQL
				select `migrated_policy_vendor`.`vendor_code` as vendor_code,
				       `policies`.`id` as policy_id,
				       `brand_maps`.`pcms_id` as brand_id,
				       `policies`.`title` as policy_title,
				       `policies`.`description` as policy_description,
				       `migrated_policy_vendor`.`shop_id` as shop_id,
				       `migrated_policy_vendor`.`type` as type,
				       `migrated_policy_vendor`.`id` as id
				from `migrated_policy_vendor`
				left join `brand_maps` on `brand_maps`.`itruemart_id` = `migrated_policy_vendor`.`brand_id`
				left join `policies` on `policies`.`id` = `migrated_policy_vendor`.`policy_id`
SQL;
			$results = DB::select($sql);

			foreach ($results as $result) {

				switch ($result->type) {
					case 'shop':
						$policiableType = "Shop";
						$policiableId = $result->shop_id;
						break;

					case 'vendor':
						$policiableType = "VVendor";
						$policiableId = $result->vendor_code;
						break;

					case 'brand':
						$policiableType = "Brand";
						$policiableId = $result->brand_id;
						break;

					default:
						throw new Exception("Wrong type. Please check db. (ID: {$result->id})");
						break;
				}

				if (! $policiableId)
				{
					continue;
				}

				$policyRelate = new PolicyRelate;
				$policyRelate->policiable_type = $policiableType;
				$policyRelate->policiable_id = $policiableId;
				$policyRelate->policy_id = $result->policy_id;
				$policyRelate->use_type = 'yes';
				try
				{
					$policyRelate->save();
				}
				catch (Exception $e)
				{
					echo $e->getMessage();
					echo " (ID: {$result->id})";
					die();
				}
			}

			echo "<br> Vendor policies complete.";

			sd(DB::getQueryLog());
		}
	}

	public function getProductExcel()
	{
		// ini_set('memory_limit','1024M');


		// DB::setDefaultConnection('pcms_migrate');
		$cmd   = Input::get('cmd');

		if ($cmd == 'run')
		{
			$file   = Input::get('file');
			//path file.
			$pathFile = "./excel-migrate-itruemart/product/$file.xlsx";
			$objPHPExcel = PHPExcel_IOFactory::load($pathFile);


			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			unset($sheetData[1]);
			$index = 0;
			$total_index_parent = ceil(count($sheetData)/100);

			$index_parent = 0;



			foreach ($sheetData as $k => $v)
			{

				if($index%$total_index_parent == 0)
				{
					$index_parent++;
				}

				$product[$index_parent][$index] = array(
						'sku_code'              =>$v['B'] ,
						'product_id'            =>$v['C'] ,
						'inventory_id'          =>$v['D'] ,
						'material_code'         =>$v['E'] ,
						'shop_id'               =>$v['F'] ,
						'brand_name'            =>htmlspecialchars($v['G'],ENT_QUOTES, 'UTF-8'),
						'barcode'               =>htmlspecialchars($v['H'],ENT_QUOTES, 'UTF-8'),
						'title'                 =>htmlspecialchars($v['I'],ENT_QUOTES, 'UTF-8'),
						'color'                 =>htmlspecialchars($v['J'],ENT_QUOTES, 'UTF-8'),
						'size'                  =>$v['K'] ,
						'normal_price'          =>$v['L'] ,
						'special_price'         =>$v['M'] ,
						'margin'                =>$v['N'] ,
						'option'                =>$v['O'] ,
						'stock'                 =>$v['P'] ,
						'vendor_code'           =>$v['Q'] ,
						'vendor_type'           =>$v['R'] ,
						'vendor_stock'          =>$v['S'] ,
						'product_status'        =>$v['T'] ,
						'create_date'           =>$v['U'] ,
						'title_eng'             =>htmlspecialchars($v['V'],ENT_QUOTES, 'UTF-8'),
						'key_feture_thai'       =>htmlspecialchars($v['W'],ENT_QUOTES, 'UTF-8'),
						'key_feture_eng'        =>htmlspecialchars($v['X'],ENT_QUOTES, 'UTF-8'),
						'description_thai'      =>htmlspecialchars($v['Y'],ENT_QUOTES, 'UTF-8'),
						'description_eng'       =>htmlspecialchars($v['Z'],ENT_QUOTES, 'UTF-8'),
						'color_code'            =>$v['AA'] ,
						'color_image'           =>$v['AB'] ,
						'size_code'             =>$v['AC'] ,
						'size_image'            =>$v['AD'] ,
						'texture'               =>$v['AE'] ,
						'texture_code'          =>$v['AF'] ,
						'texture_image'         =>$v['AG'] ,
						'product_image_original'=>$v['AH'] ,
						'product_image_big'     =>$v['AI'] ,
						'product_image_medium'  =>$v['AJ'] ,
						'product_image_thumb'   =>$v['AK'] ,
						'installment'           =>$v['AL'] ,
						'installment_period'    =>$v['AM'] ,
						'tags'                  =>htmlspecialchars($v['AN'],ENT_QUOTES, 'UTF-8'),
						'suggestions'           =>htmlspecialchars($v['AO'],ENT_QUOTES, 'UTF-8'),
						'brand_id'              =>$v['AP'] ,
						'category_id'           =>$v['AQ'] ,
						'category_name_thai'    =>htmlspecialchars($v['AR'],ENT_QUOTES, 'UTF-8'),
						'category_name_eng'     =>htmlspecialchars($v['AS'],ENT_QUOTES, 'UTF-8')
				);
				$index++;
			}

			foreach($product as $key=> $value)
			{
				MigratedProduct::insert($value);
			}
			echo '<br> MigratedProduct success<br>';

			$nextFile = $file + 1;
			// echo "<a href=\"http://pcms-true.igetapp.com/migrate-itruemart/product-excel?cmd=run&file={$nextFile}\">next page</a>";
			echo "<a href=\"http://pcms.alpha.itruemart.com/migrate-itruemart/product-excel?cmd=run&file={$nextFile}\">next page</a>";
		}


	}

}