<?php

class ProductContentController extends AdminController {

	protected $product;

    public function __construct(ProductRepositoryInterface $product)
    {
        parent::__construct();

        $this->product = $product;

        $this->theme->breadcrumb()->add('Product Content', URL::to('products/set-content'));
    }

	public function getIndex()
	{

		// $products = $this->product->executeFormSearch()
		// 	->with('brand','variants','mediaContents')
		// 	->get();

		$products = $this->product->getExecuteFormSearch();
		$products->load('brand', 'variants', 'mediaContents');

		//s(Input::all(), $products->toArray());

        $page = Input::get('page') ?: 1;
        $perPage = 10;
        $skip = $perPage * ($page-1);

        $products = Paginator::make($products->slice($skip, $perPage)->all(), $products->count(), $perPage);

		$this->theme->setTitle('List Product');
		//$view_data = compact('products');

		$this->data['products'] = $products;

        return $this->theme->of('products.set-content.index', $this->data)->render();
	}

	public function getEdit($id)
	{
        $product = Product::with(array(
        	'mediaContents'=>function($query)
        	{
        		return $query->orderBy('sort_order')->orderBy('id');
        	},
        	'styleTypes',
        	'styleOptions',
        	'variants',
        	// 'variants.mediaContents'=>function($query)
        	// {
        	// 	return $query->orderBy('sort_order')->orderBy('id');
        	// }
        ))->findOrFail($id);

        // $variantStyleOptions = array();
        $productStyleOptions = array();
        if ($product->media_style_type != false)
        {
        	$mediaSetId = $product->media_style_type->id;
        	$styleOptions = $product->styleOptions->filter(function($item) use ($mediaSetId)
        		{
        			return $item->style_type_id == $mediaSetId;
        		}
        	)->lists('id');
        	if($styleOptions != false)
        		$productStyleOptions = ProductStyleOption::where('product_id', $product->id)->whereIn('style_option_id', $styleOptions)->get();

        	// $product->load(array(
        	// 	'variants.variantStyleOptions'=>function($query) use ($mediaSetId)
	        // 	{
	        // 		return $query->where('style_type_id', $mediaSetId);
	        // 	},
	        // 	'variants.variantStyleOptions.styleOption',
        	// ));

	        // $variantStyleOptions = array();
	        // foreach ($product->variants as $variant)
	        // {
	        // 	array_push($variantStyleOptions, $variant->variantStyleOptions->first());
	        // }
        }

        $this->theme->breadcrumb()->add('Edit Product Content', URL::to('products/set-content/edit/'.$id));
        $this->theme->setTitle('Edit Product Content');

        $user = Sentry::getUser();
        $productRevisionsOfUser = $product->revisions()->whereIn('status', array('draft', 'rejected', 'approved'))->where('editor_id', $user->id)->get();

        $this->data['product'] = $product;
        $this->data['productStyleOptions'] = $productStyleOptions;
        // $this->data['variantStyleOptions'] = $variantStyleOptions;
        $this->data['revisions'] = $productRevisionsOfUser;

        return $this->theme->of('products.set-content.edit', $this->data)->render();
	}

	public function postEdit($id)
	{
		/*
        $product = Product::findOrfail($id);

        $product->key_feature = Input::get('key_feature');
        $product->description = Input::get('description');

        $product->save();
        */

        $draftData = Input::only('key_feature', 'description');
        $this->product->saveDraft($id, $draftData);

        return Redirect::to('products/set-content/');
	}

	public function postSelectMediaSet($id)
	{
		$product = Product::with(array('styleTypes'))->findOrFail($id);

		if (count($product->styleTypes)==0)
			return Redirect::back();

		$mediaSet = intval(Input::get('media-style-type'));
		foreach ($product->styleTypes as $styleType)
		{
			if ($styleType->id == $mediaSet)
			{
				$styleType->pivot->media_set = 1;
			}
			else
			{
				$styleType->pivot->media_set = 0;
			}

			$styleType->pivot->save();
		}

		return Redirect::to("/products/set-content/edit/$id#media-content");
	}

	public function postUp($mode, $modelType, $id)
	{
		if($modelType==='product')
		{
			$model = Product::find($id);
		}
		elseif($modelType==='productStyleOption')
		{
			$model = ProductStyleOption::find($id);
		}
		// elseif($modelType==='variant')
		// {
		// 	$model = ProductVariant::find($id);
		// }
		// elseif($modelType==='variantStyleOption')
		// {
		// 	$model = VariantStyleOption::find($id);
		// }

		if($model==false)
		{
			return Response::json(array(
				'success' => false,
				'error' => 'Product or Option is missing',
			));
		}

		if(($mode==='image' || $mode==='360') && Input::hasFile('uploading-image'))
		{
			$mediaContent = new MediaContent(array('mode' => $mode, 'sort_order' => 99));
			$model->mediaContents()->save($mediaContent);

			$uploadingImage = Input::file('uploading-image');
			$attachment = UP::upload($mediaContent, $uploadingImage)->resize()->getMasterResult();


			$mediaContent->attachment_id = $attachment['fileName'];
			$mediaContent->save();

			$this->updateElasticSearch($mediaContent);

			return Response::json(array(
				'success' => true,
				'media_id' => $mediaContent->id,
				'thumb' => ''.$mediaContent->thumbnail,
				'src' => ''.$mediaContent->image,
				'link' => null,
				'mode' => $mode,
				'sort_order' => $mediaContent->sort_order,
			));
		}
		elseif($mode==='youtube' && Input::has('youtube-id'))
		{
			if(Input::has('upload-screenshot'))
			{
				// extract base64 data which was sent by FileReader.readAsDataURL()
				$uploadingImage = Input::get('upload-screenshot');
				list($data, $base64) = explode(',', $uploadingImage);
				list($text, $dataType) = explode(':', $data);
				if(! in_array($dataType, array('image/jpeg;base64','image/jpg;base64','image/png;base64')))
					return Response::json(array(
						'success' => false,
						'error' => 'Accept only jpg or png image'
					));
			}

			$youtubeData = array(
				'id'	=> Input::get('youtube-id'),
				'link'	=> 'http://www.youtube.com/watch?v='.Input::get('youtube-id'),
			);
			$mediaContent = new MediaContent(array(
				'mode' => $mode,
				'meta' => json_encode($youtubeData),
				'sort_order' => 99,
			));
			$model->mediaContents()->save($mediaContent);

			if(Input::has('upload-screenshot'))
			{
				$attachment = UP::inject(array('type'=>'base64'))->upload($mediaContent, $uploadingImage)->resize()->getMasterResult();
			}
			// inject screenshot if no file selected
			else
			{
				$attachment = UP::inject(array('type'=>'remote'))->upload($mediaContent, Input::get('remote-image'))->resize()->getMasterResult();
			}

			$mediaContent->attachment_id = $attachment['fileName'];
			$mediaContent->save();

			$this->updateElasticSearch($mediaContent);

			return Response::json(array(
				'success' => true,
				'media_id' => $mediaContent->id,
				'thumb' => ''.$mediaContent->thumbnail,
				'src' => ''.$mediaContent->image,
				'link' => $youtubeData['link'],
				'mode' => $mode,
				'sort_order' => $mediaContent->sort_order,
			));
		}
	}

	public function postArrange($modelType, $id)
	{
		if($modelType==='product')
		{
			$model = Product::find($id);
		}
		elseif($modelType==='productStyleOption')
		{
			$model = ProductStyleOption::find($id);
		}
		// elseif($modelType==='variant')
		// {
		// 	$model = ProductVariant::find($id);
		// }
		// elseif($modelType==='variantStyleOption')
		// {
		// 	$model = VariantStyleOption::find($id);
		// }

		if($model==false)
		{
			return Response::json(array(
				'success' => false,
				'error' => 'Product or Option is missing',
			));
		}

		$sort_number = 0;
		$content_ids = Input::get('sortOrder');
		if(is_array($content_ids))
		{
			foreach($content_ids as $content_id)
			{
				MediaContent::where('id', $content_id)->update(array('sort_order'=>$sort_number++));
			}
			return Response::json(array(
				'success' => true,
			));
		}

		return Response::json(array(
			'success' => false,
			'error' => 'Invalid Parameter',
		));
	}

	public function postMoveToTrash($id)
	{
		$mediaContent = MediaContent::find($id);

		if ($mediaContent->mediable_type == 'Product')
		{
			$product = Product::find($mediaContent->mediable_id);
		}
		elseif ($mediaContent->mediable_type == 'ProductStyleOption')
		{
			$pso = ProductStyleOption::find($mediaContent->mediable_id);
			$product = Product::find($pso->product->id);
		}

		$deletedStatus = MediaContent::where('id', $id)->delete();

		if ($deletedStatus == TRUE && !empty($product))
		{
			ElasticUtils::updateProduct($product);
		}

		return Response::json(array(
			'success' => $deletedStatus,
		));
	}

	private function updateElasticSearch(MediaContent $mediaContent)
	{
		if ($mediaContent->mediable_type == 'Product')
		{
			$product = Product::find($mediaContent->mediable_id);
		}
		elseif ($mediaContent->mediable_type == 'ProductStyleOption')
		{
			$pso = ProductStyleOption::find($mediaContent->mediable_id);
			$product = Product::find($pso->product->id);
		}

		if (!empty($product))
		{
			ElasticUtils::updateProduct($product);
		}
	}

	public function getBulkUpload()
	{
		$this->theme->breadcrumb()->add('Bulk Upload Image', URL::to('products/set-content/bulk-upload'));
        $this->theme->setTitle('Bulk Upload Image');

        return $this->theme->of('products.set-content.bulk-upload')->render();
	}

	public function postBulkUpload()
	{
		$result = array(
			'success' => false,
		);

		if(Input::hasFile('media-file'))
		{
			$file = Input::file('media-file');
			$file_name = $file->getClientOriginalName();
			$result['id'] = $file_name;

			if( preg_match('/^((360|img|image)\-)?([0-9]+)(\-([0-9]+))?/i', $file_name, $name_parts) )
			{
				$result['fid'] = $name_parts[3];

				$mode = isset($name_parts[2]) && $name_parts[2]=='360' ? '360' : 'image';
				$file_id = $name_parts[3];
				$position = isset($name_parts[5]) ? intval($name_parts[5]) : null;
				$modelType = preg_match('/^'.Verb::getVid('product').'[0-9]{10,}$/', $file_id) ? 'product' : 'variant';

				if($modelType==='product')
				{
					$model = Product::with(array('mediaContents'=>function($query)
					{
						return $query->orderBy('sort_order');
					}))
					->where('pkey', $file_id)->first();
				}
				elseif($modelType==='variant')
				{
					$model = ProductVariant::with(array('mediaContents'=>function($query)
					{
						return $query->orderBy('sort_order');
					}))
					->where('inventory_id', $file_id)->first();
				}

				if($model==false)
				{
					if($modelType==='product')
						$result['error'] = 'Invalid Product Key';
					else
						$result['error'] = 'Invalid Inventory ID';

					return Response::json($result, 500);
				}
				else
				{
					$old_contents = $model->mediaContents;
					if($position!==null && $old_contents->count()>0)
					{
						$counter = $position+1;
						for($i=$position; $i<count($old_contents); $i++)
						{
							$old_contents[$i]->update( array('sort_order' => $counter++) );
						}
					}

					$mediaContent = new MediaContent(array('mode' => $mode, 'sort_order' => ($position!==null ? $position : 99) ));
					$model->mediaContents()->save($mediaContent);

					$attachment = UP::upload($mediaContent, $file)->resize()->getMasterResult();
					$attachment_id = ''.$attachment['fileName'];

					$mediaContent->attachment_id = $attachment_id;
					$mediaContent->save();

					$result['success'] = true;
					$result['thumb'] = ''.$mediaContent->thumbnail;
					$result['src'] = ''.$mediaContent->image;
					$result['mode'] = $mode;
					$result['sort_order'] = $mediaContent->sort_order;

					if(preg_match_all('/,((360|img|image)\-)?([0-9]+)(\-([0-9]+))?/i', $file_name, $name_parts))
					{
						$extra_content_count = count($name_parts[0]);
						for($k=0; $k<$extra_content_count; $k++)
						{
							$result['fid'] = $name_parts[3][$k];

							$mode = isset($name_parts[2][$k]) && $name_parts[2][$k]=='360' ? '360' : 'image';
							$file_id = $name_parts[3][$k];
							$position = isset($name_parts[5][$k]) ? intval($name_parts[5][$k]) : null;
							$modelType = preg_match('/^'.Verb::getVid('product').'[0-9]{13,}$/', $file_id) ? 'product' : 'variant';

							if($modelType==='product')
							{
								$model = Product::with(array('mediaContents'=>function($query)
								{
									return $query->orderBy('sort_order');
								}))
								->where('pkey', $file_id)->first();
							}
							elseif($modelType==='variant')
							{
								$model = ProductVariant::with(array('mediaContents'=>function($query)
								{
									return $query->orderBy('sort_order');
								}))
								->where('inventory_id', $file_id)->first();
							}

							if($model==false)
								continue;

							$old_contents = $model->mediaContents;
							if($position!==null && $old_contents->count()>0)
							{
								$counter = $position+1;
								for($i=$position; $i<count($old_contents); $i++)
								{
									$old_contents[$i]->update( array('sort_order' => $counter++) );
								}
							}

							$mediaContent = new MediaContent(array(
								'mode' => $mode,
								'attachment_id' => $attachment_id,
								'sort_order' => ($position!==null ? $position : 99)
							));
							$mediaContent->attachment_id = $attachment_id;
							$model->mediaContents()->save($mediaContent);
						}
					}

					return Response::json($result, 200);
				}
			}
		}
		$result['error'] = 'Invalid File';
		return Response::json($result, 500);
	}

}