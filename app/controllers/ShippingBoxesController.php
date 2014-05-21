<?php

class ShippingBoxesController extends AdminController {

	public function __construct()
	{
		parent::__construct();
		$this->theme->breadcrumb()->add('Shipping Box Management', URL::to('shipping/boxes'));
	}

	public function getIndex()
	{
		$boxes = ShippingBox::all();

		$this->data['boxes'] = $boxes;
		$this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

		$this->theme->setTitle('Shipping Box Management');
		return $this->theme->of('shipping.boxes.index', $this->data)->render();
	}

	public function getCreate()
	{
		$this->data['formData'] = array(
									'name'   => '',
									'weight' => '',
									'price'  => '',
									);

		$this->theme->breadcrumb()->add('Create Shipping Box', URL::to('shipping/boxes/create'));
		$this->theme->setTitle('Create Shipping Box');
		return $this->theme->of('shipping.boxes.create-edit', $this->data)->render();
	}

	public function postCreate()
	{
		$boxes = new ShippingBox;
		$boxes->name   = Input::get('name');
		$boxes->weight = Input::get('weight');
		$boxes->price  = Input::get('price');

		if (!$boxes->save())
		{
			return Redirect::to('shipping/boxes/create')->withInput()->withErrors($boxes->errors());
		}

		return Redirect::to('shipping/boxes');
	}

	public function getEdit($id = 0)
	{
		$boxes = ShippingBox::findOrFail($id);

		$this->data['formData'] = array(
			'name'   => $boxes->name,
			'weight' => $boxes->weight,
			'price'  => $boxes->price,
		);

		$this->theme->breadcrumb()->add('Edit Shipping Box', URL::to('shipping/boxes/edit/'.$id));
		$this->theme->setTitle('Edit Shipping Box');
		return $this->theme->of('shipping.boxes.create-edit', $this->data)->render();
	}

	public function postEdit($id = 0)
	{
		$boxes = ShippingBox::findOrFail($id);
		$boxes->name   = Input::get('name');
		$boxes->weight = Input::get('weight');
		$boxes->price  = Input::get('price');

		if (!$boxes->save())
		{
			return Redirect::to("shipping/boxes/edit/{$id}")->withInput()->withErrors($boxes->errors());
		}

		return Redirect::to('shipping/boxes');
	}

	public function getDelete($id)
	{
		$boxes = ShippingBox::findOrFail($id);
		$boxes->delete();

		return Redirect::back();
	}

	public function postDelete($id=0)
	{
		$boxes = ShippingBox::findOrFail($id);
		$boxes->delete();

		return Redirect::back();
	}

	public function getTrashed()
	{
		//get soft deleted models.
		$boxes = ShippingBox::onlyTrashed()->get();

		//$boxes = ShippingBox::withTrashed()->get();
		$this->data['boxes'] = $boxes;

		$this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');
		$this->theme->breadcrumb()->add('Trashed Shipping Box', URL::to('shipping/boxes/trashed/'));

		return $this->theme->of('shipping.boxes.trashed', $this->data)->render();
	}

	public function getUndo( $id = 0)
	{
		$box = ShippingBox::onlyTrashed()->findOrFail($id);
		//restore a soft deleted model.
		$box->restore();

		return Redirect::back();
	}

}