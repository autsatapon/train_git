<?php

class PaymentController extends BaseController {

    private $order, $stock;
    private $wetrust;

    public function __construct(OrderRepositoryInterface $order, StockRepositoryInterface $stock)
    {
        parent::__construct();

        $this->order = $order;
        $this->stock = $stock;

        $this->wetrust = App::make('wetrust');
    }

    // not real
    public function getProcess()
    {
        $html = $this->wetrust->generateHTMLSubmitForm(Order::find(Input::get('order_id')));

        echo $html;
    }

    // not real
    public function getFgResponse()
    {
        if (Input::has('success')) // offline
        {
            return 'Thank you (Offline)';
        }
        else // online
        {
            $raw = file_get_contents('php://input');
            $array = \Wetrust\Format::factory($raw, 'xml')->toArray();
            $orderId = array_get($array, 'payment.@attributes.ref3', 21);

            $response = API::get('/api/45311375168544/payment/check-status', array('order_id' => $orderId));

            if ($response['data']['payment_status'] == 'approved')
            {
                return 'Thank you (Online)';
            }
            else
            {
                return 'requery button';
            }
        }
    }

    // background from wetrust
    public function anyProcessCallback()
    {
//		$raw = '702acdbd795ca35ea8a96ac1ad32c545353758f82ea23d854346711bdf60ffea9a8d94e78ff1c5bb26fb1c9d86717dde8cb6b3c35f28dcff4bd704f2b45a28a42e0e3eede14241b312f48b2dea4254d19b6693e7b8081fc6effedc6b885d040287e467a15f299a94b1eec10ea46626b4427030ec4eaf975f7102eb30ce18d29759053fed1af28652b26c9c5c40e1c85a22d26c88e6a025ab74cf41608f6cd47360edfe1d3a602f12df7ae105db5b649824f9c577719282af3c554bf0f14eae776d20967d649c60f7f98d39e061b58369c4daa7d00f271fcef9b4a2fb3bf00d2ced7a242dfc423812fcab95e118e1f1866ab949a6dd71ea896f4be137545bed10063a0d9a7b938a195430ef7ee327c6593598d4816ab924814c676e4bad9a85855609845ba1a525ae26701c8defe1b894520ac8ce517c9847bc42aa49dd6bee5f3ed8418c11f474ab6598aa394f145fc5e53c4a6c228fc05f439a57abe3aa9d83d35d75158e25876bd6b0e926659e44ca57cb31197fb9b7';
        $raw = file_get_contents('php://input');

        // decrypt RC4 to Array
        $wetrustData = $this->wetrust->BGCallbackResponse($raw);

        $array = array(
            'REQUEST_METHOD' => Request::server('REQUEST_METHOD'),
            'Input'          => Input::all(),
            'raw'            => $raw,
            'wetrustData'    => $wetrustData
        );
        $tmp = new Tmp;
        $tmp->key = 'background-callback';
        $tmp->value = json_encode($array);
        $tmp->save();

        // get order
        $order = Order::findOrFail($wetrustData['ref3']);

        $payDate = array_get($wetrustData, 'pay_date');
        $payDate = preg_replace('/(\d{2})(\d{2})(\d{4})(\d{2})(\d{2})(\d{2})/', '$3-$2-$1 $4:$5:$6', $payDate);

        $reconcileData = array(
            'ref1'             => array_get($wetrustData, 'ref1', $order->ref1),
            'ref2'             => array_get($wetrustData, 'ref2', $order->ref2),
            'ref3'             => array_get($wetrustData, 'ref3'),
            'payment_order_id' => array_get($wetrustData, 'orderidwls'),
            'transaction_time' => $payDate
        );
        $tmp2 = new Tmp;
        $tmp2->key = 'background-reconcileData';
        $tmp2->value = json_encode($reconcileData);
        $tmp2->save();

        // save reconcile order
        $paymentRepo = App::make('PaymentRepositoryInterface');
        $paymentRepo->saveReconcile($order, $reconcileData);
    }

    // reconcile cron
    public function postReconcile()
    {
        $orderId = Input::get('order_id');

        $order = Order::findOrFail($orderId);

        $paymentRepo = App::make('PaymentRepositoryInterface');
        $paymentRepo->checkReconcile($order);
    }

}

