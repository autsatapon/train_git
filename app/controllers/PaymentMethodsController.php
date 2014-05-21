<?php

class PaymentMethodsController extends AdminController {

	public function __construct()
	{
		parent::__construct();
		$this->theme->breadcrumb()->add('PaymentMethod Management', URL::to('shipping/payment-methods'));
	}

	public function getIndex()
	{
		$payment_methods = PaymentMethod::all();

		$this->data['payment_methods'] = $payment_methods;
		$this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

		$this->theme->setTitle('Payment Method Management');
		return $this->theme->of('shipping.payment-method.index', $this->data)->render();
	}

	public function getCreate()
	{
		$this->data['formData'] = array(
									'name'   => '',
									'code' => '',
									'channel'	=> '',
									'transaction_fee' => '',
									'transaction_apply' => ''
									);

		$this->theme->breadcrumb()->add('Create Payment Method', URL::to('shipping/paymentmethod/create'));
		$this->theme->setTitle('Create Payment Method');
		return $this->theme->of('shipping.payment-method.create-edit', $this->data)->render();
	}

	public function postCreate()
	{

		$payment_methods = new PaymentMethod;
		$payment_methods->name   = Input::get('name');
		$payment_methods->code = Input::get('code');
		$payment_methods->channel  = Input::get('channel');
		$payment_methods->transaction_fee  = Input::get('transaction_fee');
		$payment_methods->transaction_apply  = Input::get('transaction_apply');

		if (!$payment_methods->save())
		{
			return Redirect::to('shipping/payment-methods/create')->withInput()->withErrors($payment_methods->errors());
		}

		return Redirect::to('shipping/payment-methods');
	}

	public function getEdit($id = 0)
	{
		$payment_methods = PaymentMethod::findOrFail($id);

		$this->data['formData'] = array(
			'name' => $payment_methods->name,
			'code' => $payment_methods->code,
			'channel'  => $payment_methods->channel,
			'transaction_fee' => $payment_methods->transaction_fee,
			'transaction_apply' => $payment_methods->transaction_apply,
		);

		$this->theme->breadcrumb()->add('Edit Payment Method', URL::to('shipping/payment-methods/edit/'.$id));
		$this->theme->setTitle('Edit Payment Method');
		return $this->theme->of('shipping.payment-method.create-edit', $this->data)->render();
	}

	public function postEdit($id = 0)
	{
		$payment_methods = PaymentMethod::findOrFail($id);
		$payment_methods->name   = Input::get('name');
		$payment_methods->code = Input::get('code');
		$payment_methods->channel  = Input::get('channel');
		$payment_methods->transaction_fee  = Input::get('transaction_fee');
		$payment_methods->transaction_apply  = Input::get('transaction_apply');

		if (!$payment_methods->save())
		{
			return Redirect::to("shipping/payment-methods/edit/{$id}")->withInput()->withErrors($boxes->errors());
		}

		return Redirect::to('shipping/payment-methods');
	}

	public function getDelete($id)
	{
		$payment_methods = PaymentMethod::findOrFail($id);
		$payment_methods->delete();

		return Redirect::back();
	}

	public function postDelete($id=0)
	{
		$payment_methods = PaymentMethod::findOrFail($id);
		$payment_methods->delete();

		return Redirect::back();
	}

	public function getTrashed()
	{
		//get soft deleted models.
		$payment_methods = PaymentMethod::onlyTrashed()->get();

		//$boxes = ShippingBox::withTrashed()->get();
		$this->data['payment_methods'] = $payment_methods;

		$this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');
		$this->theme->breadcrumb()->add('Trashed Payment Method', URL::to('shipping/payment-methods/trashed/'));

		return $this->theme->of('shipping.payment-method.trashed', $this->data)->render();
	}

	public function getUndo( $id = 0)
	{
		$payment_method = PaymentMethod::onlyTrashed()->findOrFail($id);
		//restore a soft deleted model.
		$payment_method->restore();

		return Redirect::back();
	}

}