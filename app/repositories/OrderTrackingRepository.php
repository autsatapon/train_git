<?php

class OrderTrackingRepository {

	protected $wetrust;

	protected $order;
	protected $message;

	// /**** payment ****/
	// public function requery(Order $order)
	// {
	// 	$wetrust = $this->loadWetrust();

	// 	$result = $wetrust->requery($order->id);

	// 	// if approved update order status
	// 	if (array_get($result, 'respcode') == '0' && strtolower(array_get($result, 'respdesc')) === 'approved') // <respdesc>Approved</respdesc>
	// 	{
	// 		// $order->ref1 = array_get($result, 'ref1');
	// 		// $order->ref2 = array_get($result, 'ref2');
	// 		// $order->ref3 = array_get($result, 'ref3');
	// 		$order->payment_order_id = array_get($result, 'orderidwls');
	// 		$order->status = Order::STATUS_NEW;
	// 		$order->payment_status = Order::PAYMENT_SUCCESS;
	// 		$order->save();

	// 		return true;
	// 	}

	// 	return false;
	// }


	// /**** tracking ****/
	// public function setPaymentStatus(Order $order, $paymentStatus)
	// {
	// 	$paymentChannel = strtolower($order->payment_channel);

	// 	// update related status
	// 	switch ($paymentStatus) {
	// 		case Order::PAYMENT_WAITING:
	// 			$this->setOrderStatus($order, Order::STATUS_WAITING);
	// 			break;

	// 		case Order::PAYMENT_SUCCESS:
	// 			// update nothing; wait until reconciled
	// 			break;

	// 		case Order::PAYMENT_FAILED:
	// 			// update nothing; wait until expired
	// 			break;

	// 		case Order::PAYMENT_RECONCILE:
	// 			$this->setOrderStatus($order, Order::STATUS_NEW);
	// 			break;

	// 		case Order::PAYMENT_EXPIRE:
	// 			$this->setOrderStatus($order, Order::STATUS_EXPIRE);

	// 		default:
	// 			return false;
	// 	}

	// 	// set if status exists
	// 	$order->payment_status = $paymentStatus;
	// 	return true;
	// }

	// public function setGiftStatus(Order $order, $giftStatus = null)
	// {
	// 	switch ($giftStatus) {
	// 		case Order::GIFT_ADDED:
	// 			break;
			
	// 		case Order::GIFT_EMPTY:
	// 			break;
			
	// 		case Order::GIFT_REJECTED:
	// 			break;
			
	// 		case Order::GIFT_CONFIRMED:
	// 			$this->setOrderStatus($order, Order::STATUS_PREPARING);
	// 			break;
			
	// 		default:
	// 			# code...
	// 			break;
	// 	}
	// }

	// public function setOrderStatus(Order $order, $orderStatus)
	// {
	// 	switch ($orderStatus) {
	// 		case Order::STATUS_WAITING:
	// 			break;
			
	// 		case Order::STATUS_NEW:
	// 			break;
			
	// 		case Order::STATUS_GIFT_ADDED:
	// 		case Order::STATUS_NO_GIFT:
	// 			break;
			
	// 		case Order::STATUS_GIFT_REJECTED:
	// 			break;

	// 		case Order::STATUS_GIFT_CONFIRMED:
	// 			break;
			
	// 		case Order::STATUS_WAITING:
	// 			break;
			
	// 		case Order::STATUS_WAITING:
	// 			break;
			
	// 		default:
	// 			return false;
	// 	}

	// 	return true;
	// }

	// /**** getters & setters ****/
	// public function loadWetrust()
	// {
	// 	if ($this->wetrust == false)
	// 		$this->wetrust = App::make('wetrust');
	// 	return $this->wetrust;
	// }

}