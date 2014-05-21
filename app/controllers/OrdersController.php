<?php

class OrdersController extends AdminController {

    protected $order;

    public function __construct(OrderRepositoryInterface $order)
    {
        parent::__construct();

        $this->order = $order;

        $this->theme->breadcrumb()->add('Orders', URL::to('orders'));

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');
    }

    public function getIndex($tab=null, $orders=null ,$dateData = null ,$status = null)
    {
        $user = Sentry::getUser();

        $group = $user->getGroups();

        $role = $group[0]->getKey();

        if (is_null($tab))
        {
            return Redirect::to('orders/task');
        }

        $this->theme->setTitle('Order Tracker');

        $view = compact('tab', 'user', 'orders', 'role','dateData','status');

        $this->theme->asset()->usePath()->add('orders-index', 'admin/css/orders-index.css', array('style-mws-style', 'style-mws-theme'));

        return $this->theme->of('orders.index', $view)->render();
    }

    public function getTask()
    {
        $user = Sentry::getUser();

        $orders = $this->order->getTask($user);

        return $this->getIndex('task', $orders);
    }

    public function getSearch()
    {
        $user = Sentry::getUser();

        $orders = $this->order->getSearch($user);

        return $this->getIndex('search', $orders);
    }

    public function getAll()
    {

        $orders = $this->order->getAll();

        return $this->getIndex('all', $orders);
    }

	public function getStatus($status)
    {
        $orders = $this->order->getByStatus($status);

        return $this->getIndex($status, $orders);
    }

	public function getDatedata($dateData,$status = 'all')
    {
        $orders = $this->order->getByDatedata($dateData,$status);

        return $this->getIndex($status, $orders,$dateData,$status);
    }

    public function getClosed()
    {

        $orders = $this->order->getClosed();

        return $this->getIndex('closed', $orders);
    }

    public function getDetail($id)
    {
        $user = Sentry::getUser();

        $order = Order::with(array('shipments', 'shipmentItems', 'orderLogs', 'orderLogs.actor', 'orderLogs.actor.groups', 'orderNotes','orderAddressLog.user.groups', 'orderAddressLog' => function($query) { return $query->orderBy('id', 'desc'); } ))->findOrFail($id); 

        $view = compact('user', 'order');

        $this->theme->asset()->usePath()->add('orders-detail', 'admin/css/orders-detail.css', array('style-mws-style', 'style-mws-theme'));

        return $this->theme->of('orders.detail', $view)->render();
    }

    public function postDetail($id)
    {
        if (Input::has('order'))
        {
			 $user = Sentry::getUser();
            foreach (Input::get('order') as $id => $value)
            {
                $order = Order::find($id);
				$order_address_logs = new OrderAddressLog();

				$order_address_logs->order_id = $order->id;
				$order_address_logs->actor_id = $user->id;
				$order_address_logs->name = $order->customer_name;
				$order_address_logs->address = $order->customer_address;
				$order_address_logs->tel = $order->customer_tel;
				$order_address_logs->province = $order->customer_province;
				$order_address_logs->	postcode = $order->customer_postcode;

				$order_address_logs->save();

                $order->customer_name = $value['customer_name'];
                $order->customer_address = $value['customer_address'];
                $order->customer_province = $value['customer_province'];
                $order->customer_postcode = $value['customer_postcode'];
                $order->customer_tel = $value['customer_tel'];

                $order->save();
				
				
				$countOrder = OrderAddressLog::where('order_id', $order->id)->count();
				if($countOrder >= 10){
					$orderDel = OrderAddressLog::where("order_id", $order->id)->orderBy('id', 'asc')->first();
					$orderDel->delete();
				}

			/*	$order_address_logs = new OrderAddressLog();

				$order_address_logs->order_id = $order->id;
				$order_address_logs->actor_id = $user->id;
				$order_address_logs->name = $value['customer_name'];
				$order_address_logs->address = $value['customer_address'];
				$order_address_logs->tel = $value['customer_tel'];
				$order_address_logs->province = $value['customer_province'];
				$order_address_logs->	postcode = $value['customer_postcode'];

				$order_address_logs->save(); */

            }
        }

        if (Input::has('shipment'))
        {
            foreach (Input::get('shipment') as $id => $value)
            {
                $shipment = OrderShipment::find($id);

                $shipment->tracking_number = $value['tracking_number'];
                $shipment->shipment_status = $value['shipment_status'];

                $shipment->save();
            }
        }

        if (Input::has('items'))
        {
            foreach (Input::get('items') as $id => $value)
            {
                $items = OrderShipmentItem::find($id);

                $items->item_status = $value['item_status'];
				$items->tracking_number = $value['tracking_number'];

                $items->save();
            }
        }

        return Redirect::back();
    }

    public function getActions($id, $action)
    {
        $order = Order::findOrFail($id);

        switch ($action)
        {
            case 'check':
                $order->order_status = 'checked';
                break;

            case 'finish':
                $has_incomplete_shipment = false;
                foreach($order->shipments as $shipment)
                {
                    if($shipment->shipment_status!=='delivered')
                    {
                        $has_incomplete_shipment = true;
                        break;
                    }
                }
                $order->order_status = ( $has_incomplete_shipment===false ? 'completed' : 'done' );
                break;

            case 'confirm_address':
                $order->customer_status = 'address confirmed';
                $order->customer_sla_time_at = null;
                break;

            case 'call_later':
                $order->customer_status = 'call later';
                $order->customer_sla_time_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                break;

            case 'unreachable':
                $order->customer_status = 'unreachable';
                $order->customer_sla_time_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                break;

            case 'dismiss_contact_modification':
                $order->customer_info_modified_at = null;
                break;

            case 'add_gift':
                $user = Sentry::getUser();

                $order = Order::with(array('shipments', 'shipmentItems', 'orderLogs', 'orderLogs.actor', 'orderLogs.actor.groups'))->findOrFail($id);
                $view = compact('user', 'order');

                $this->theme->asset()->usePath()->add('orders-detail', 'admin/css/orders-detail.css', array('style-mws-style', 'style-mws-theme'));
                return $this->theme->of('orders.addgift', $view)->render();

            default:
                $order->order_status = strtolower($action);
				if(strtolower($action) == PaymentStatus::getStatus(PaymentStatus::$code['refund'])){
					$order->payment_status = PaymentStatus::getStatus(PaymentStatus::$code['refund']);
				}
                break;
        }

        $order->save();

        return Redirect::back();
    }

    public function postNote($id)
    {
        $order = Order::findOrFail($id);

        $note = new OrderNote();
        $note->note_to = Input::get('note-to');
        $note->detail = Input::get('note-message');

        $order->orderNotes()->save($note);

        return Redirect::back();
    }

    public function postReadNote($id)
    {
        $note = OrderNote::where('id', $id)->first();
        if($note==false)
            return Response::json(array(false));

        $note->mentioned_at = date('Y-m-d H:i:s');
        $note->save();
        return Response::json(array(true));
    }

    public function getDeleteNote($id)
    {
        $note = OrderNote::findOrFail($id);
        $note->delete();

        return Redirect::back();
    }

    public function postAddGift($id)
    {
        $order = Order::findOrFail($id);
        $gifts = Input::get('giftQuantity');
        $newGifts = Input::get('newGiftQuantity');

        $inventory_ids = array();

        if($gifts!=false)
        {
            foreach($gifts as $gift_id=>$quantity)
            {
                $gift = OrderShipmentItem::where('order_id', $id)->where('inventory_id', $gift_id)->where('is_gift_item','1')->first();
                if($gift==false)
                    continue;

                if($quantity>0)
                {
                    $gift->quantity = $quantity;
                    $gift->save();

                    $inventory_ids[$gift_id] = $quantity;
                }
                else
                {
                    $gift->delete();
                }
            }
        }

        if($newGifts!=false)
        {
            foreach($newGifts as $gift_id=>$quantity)
            {
                if( !isset($inventory_ids[$gift_id]) )
                {
                    $gift = new OrderShipmentItem();
                    $gift->order_id = $id;
                    $gift->inventory_id = $gift_id;
                    $gift->quantity = $quantity;
                    $gift->is_gift_item = '1';
                    $gift->save();
                }
            }
        }

        return Redirect::back();
    }

    public function getRemoveGift($id)
    {
        $item = OrderShipmentItem::findOrFail($id);
        $item->delete();

        return Redirect::back();
    }

    public function getDiscountTracker($orderId = 0)
    {
        if ($orderId > 0)
        {
            return $this->discountTracker($orderId);
        }

        return $this->discountTrackerAll();
    }

    protected function discountTracker($orderId)
    {
        // $order = Order::with(array('shipments', 'shipments.shipmentItems'))->findOrFail($orderId);

        // $this->data['order'] = $order;

        $order = Order::findOrFail($orderId);
        $orderTransactions = OrderTransaction::where('order_id', $orderId)->orderBy('id', 'asc')->get();

        $this->data['order'] = $order;
        $this->data['orderTransactions'] = $orderTransactions;
        $this->data['allCustomerPay'] = array_sum($orderTransactions->lists('customer_pay'));

        $this->theme->breadcrumb()->add('Orders Discount Tracker', URL::to('orders/discount-tracker'));
        $this->theme->breadcrumb()->add('Detail', URL::to('orders/discount-tracker/'. $orderId));
        $this->theme->setTitle('Order Discount Tracker');

        return $this->theme->of('orders.discount-tracker-detail', $this->data)->render();
    }

    protected function discountTrackerAll()
    {
        $orderTransactions = OrderTransaction::orderBy('id', 'asc')->get();
        $orderIdArr = array_unique($orderTransactions->lists('order_id'));

        $orders = Order::whereIn('id', $orderIdArr)->get();

        foreach ($orders as $order)
        {
            $orderId = $order->id;
            $filterRs = $orderTransactions->filter( function($transaction) use($orderId) {
                return ($transaction->order_id == $orderId);
            });
            $order->all_customer_pay = array_sum($filterRs->lists('customer_pay'));
        }

        $this->data['orders'] = $orders;

        $this->theme->asset()->container('footer')->usePath()->add('jquery-datatables', 'plugins/datatables/jquery.dataTables.min.js', 'jquery');

        $this->theme->breadcrumb()->add('Orders Discount Tracker', URL::to('orders/discount-tracker'));
        $this->theme->setTitle('Order Discount Tracker');

        return $this->theme->of('orders.discount-tracker', $this->data)->render();
    }

	public function postInvoice($id)
    {
        $order = Order::findOrFail($id);
        $order->invoice = Input::get('invoiceCode');
        $order->save();

        return Redirect::back();
    }

}