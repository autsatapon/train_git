<?php

class MessageRepository implements MessageRepositoryInterface {

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    protected $order;
    protected $actionName;
    protected $customerType;
    protected $instance = false;
    public $lang;

    public function thanksForOrdering(Order $order)
    {
        $this->createInstance($order, "thankyou-ordering");
    }

    public function pleasePayYourOrder(Order $order)
    {
        $this->createInstance($order, "pay-reminder");
    }

    public function yourOrderWasShipped(Order $order)
    {
        $this->createInstance($order, "order-shipped");
    }

    public function thanksForYourPayment(Order $order)
    {
        $this->createInstance($order, "thankyou-page");
    }

    protected function createInstance(Order $order, $actionName)
    {
        $this->order      = $order;
        $this->actionName = $actionName;
        $this->instance   = true;
    }

    public function setCustomerType($customerType)
    {
        $this->customerType = $customerType;
    }

    public function getActionName()
    {
        return $this->actionName;
    }

    public function send($lang, $base_url)
    {
        if (! $this->instance)
        {
            throw new Exception("You cannot send message directly without call method that will create repository instance.");
        }

        if (! $this->customerType)
        {
            throw new Exception("Please set customer type before getMessages()");
        }

        $messages = $this->getMessages($lang);
        $this->sendMessages($messages, $lang, $base_url);
    }

    protected function getMessages($lang)
    {
        if (! $this->order->messages)
        {
            $this->order->load('messages');
        }

        $that = $this;

        $messages = $this->order->messages->filter(function($item) use ($that) {
            return ($item->action_name == $that->getActionName());
        });

        $email = $messages->filter(function($item) {
            return ($item->channel == 'gmail');
        })->first();

        $sms = $messages->filter(function($item) {
            return ($item->channel == 'sms');
        })->first();

        if (! $email)
        {
            $orderApp = PApp::find($this->order->app_id);
            $appSlug = $orderApp->slug;

            // create email
            $mail = new Message;
            $mail->action_name     = 'thankyou-page';
            if ($lang == 'en')
            {
                $mail->subject = $appSlug . ': ' . $this->order->customer_name . ': Your order no. ' . $this->order->payment_order_id . '('.$this->order->order_id.')';
            }
            else
            {
                $mail->subject = $appSlug . ': ' . $this->order->customer_name . ': แจ้งรายการสั่งซื้อสินค้า เลขที่การสั่งซื้อ ' . $this->order->payment_order_id . '('.$this->order->order_id.')';
            }
            $mail->send_to         = $this->order->customer_email;
            $mail->channel         = 'gmail';
            $mail->status          = 'queue';
            $this->order->messages()->save($mail);
        }

        if (! $sms)
        {
            // create sms
            $sms = new Message;
            $sms->action_name = 'thankyou-page';
            $sms->subject     = $this->subject_sms($this->order);
            $sms->content     = $this->subject_sms($this->order);
            $sms->send_to     = $this->order->customer_tel;
            $sms->channel     = 'sms';
            $sms->status      = 'queue';
            #Create SMS
            $this->order->messages()->save($sms);
        }

        $this->order->load('messages');

        return $this->order->messages;
    }

    protected function sendMessages($messages, $lang, $base_url)
    {
        
        foreach ($messages as $item) {

            if ($item->channel == 'gmail')
            {
                if($item->status == 'queue')
                {
                    $data = array();
                    $data['order_id']      = $this->order->order_id;
                    $data['send_to']       = $item->send_to;
                    $data['subject']       = $item->subject;
                    $data['customer_type'] = $this->customerType;
                    $data['pkey']          = $this->order->pkey;
                    $data['lang']          = $lang;
                    $data['base_url']      = $base_url;

                    $result = $this->sendEmail($data);
                    #update
                    if (array_get($result, 'header.code') == '200')
                    {
                        $item->status = 'sent';
                        $item->save();
                    }
                    else
                    {
                        $item->status = 'failed';
                        $item->save();
                    }
                }
                else
                {
                    $googleAnalytics = Order::find($this->order->order_id);
                    $googleAnalytics->analytics_status = '1';
                    $googleAnalytics->save();
                }
            }

            if ($item->channel == 'sms')
            {
                if($item->status == 'queue')
                {
                    $data = array();
                    $data['phone_no'] = $item->send_to;
                    $data['message']  = $item->content;
                    $result = $this->sendSMS($data);

                    #update
                    if ($result == true)
                    {
                        $item->status = 'sent';
                        $item->save();
                    }
                    else
                    {
                        $item->status = 'failed';
                        $item->save();
                    }
                }
            }
        }
    }

    public function sendEmail($data)
    {

        $template = $this->getEmailTemplate($data);

        $email = new Email;
        $response = $email->send($data['send_to'], $data['subject'] , $template, 'google');
        // Send to Email Marketing
        $items = null;
        $email_marketing = Config::get('email_template.email_marketing');
        foreach ($email_marketing as $item)
        {
            $items = $item.',';
        }
        $email->send($items, $data['subject'], $template, 'true');
        return json_decode($response,true);
    }

    public function sendSMS($data)
    {
        if (empty($data)) return null;

        $sms = new SMS;
        $response = $sms->send($data['phone_no'], $data['message']);
        return json_decode($response,true);
    }

    public function getPaymentMethodThai($paymentMethodThai)
    {
        switch (strtolower($paymentMethodThai))
        {
            case '8':
                return 'เคาน์เตอร์เซอร์วิส';
                break;
            case '1':
                return 'บัตรเครดิต';
                break;
            case '2':
                return 'ทรูมันนี่ วอเลท';
                break;
            case '3':
                return 'ผ่อนชำระ';
                break;
            case '4':
                return 'ตู้ ATM';
                break;
            case '5':
                return 'iBanking';
                break;
            case '6':
                return 'เคาน์เตอร์ธนาคาร';
                break;
            case '7':
                return 'จ่ายเมื่อได้รับสินค้า';
                break;
            default:
                return null;
                break;
        }
    }

    public function getPaymentMethod($payment_method)
    {
        switch (strtolower($payment_method))
        {
            case '8':
                return 'Couter Service';
                break;
            case '1':
                return 'Credit Card';
                break;
            case '2':
                return 'Truemoney Ewallet';
                break;
            case '3':
                return 'Installment';
                break;
            case '4':
                return 'Bank ATM';
                break;
            case '5':
                return 'Internet Banking';
                break;
            case '6':
                return 'Payment Counter';
                break;
            case '7':
                return 'Cash on Delivery';
                break;
            default:
                return null;
                break;
        }

    }

    public function getPaymentStatus($payment_status)
    {
        switch ($payment_status) {
            case 'waiting':
                return 'รอการชำระเงิน';
                break;
            case 'checking':
                return 'รอการตรวจสอบ';
                break;
            case 'paid':
                return 'ชำระเงินแล้ว';
                break;
            case 'reconcile':
                return 'ชำระเงินแล้ว';
                break;
            default:
                return null;
                break;
        }
    }

    public function subject_sms($order)
    {
        $quantity = 0;
        foreach($order->shipmentItems as $item)
        {
            $quantity += $item->quantity;
        }

        $payment_method      = $this->getPaymentMethod($order->payment_method);
        $payment_method_thai = $this->getPaymentMethodThai($order->payment_method);
        $payment_status      = $this->getPaymentStatus($order->payment_status);
        
        $subject  = 'ท่านได้สั่งซื้อ รายการ ' . $order->order_ref;
        $subject .= '('.$order->order_id.') จำนวน ';
        $subject .= $quantity.' ชิ้น ราคารวม '.number_format($order->sub_total,2). ' บาท ('.$payment_method_thai.' ('.$payment_method.'): ';
        $subject .= $payment_status.') ขอบคุณที่ใช้บริการค่ะ';
        return $subject;
    }

    public function getEmailTemplate($data)
    {

//        $order     = Order::with(array('shipments','shipments.shipmentItems','shipments.shipmentItems.variant','shipments.shipmentItems.variant.product','shipments.shipmentItems.variant.product.mediaContents'))->findOrFail($data['order_id']);
        $order     = Order::with(array('shipments.shipmentItems'))->findOrFail($data['order_id']);
        $shipments = $order->shipments;

        $orderApp = PApp::find($order->app_id);
        $appSlug = $orderApp->slug;

        // get config md5()
        $key = md5(Config::get('email_template.md5_salt').$data['pkey']);
		
		// $front_url = Config::get('email_template.front_url');
        $front_url = $data['base_url'];

        if ($data['customer_type'] == 'non-user')
        {
            $link = $front_url.'order_tracking?order='.$data['pkey'].'&code='.$key;
            
            if ($appSlug == 'campusstore')
            {
                $thank_link = $front_url . '/checkout/thank-you?order_id='.$data['order_id'];
            }
            else
            {
                $thank_link = $front_url . '/checkout/thank-you?order_id='.$data['order_id'];
            }
        }
        else
        {
            $link = $front_url.'member/orders';

            if ($appSlug == 'campusstore')
            {
                $thank_link = $front_url . '/checkout/thank-you?order_id='.$data['order_id'];
            }
            else
            {
                $thank_link = $front_url.'/checkout/thank-you?order_id='.$data['order_id'];
            }
        }
        //$order     = $data['order'];
        //$shipments = $data['shipments'];


        if ($order->payment_status == 'success')
        {
            $payment_status = __('รอการตรวจสอบ');
        }
        else if ($order->payment_status == 'waiting')
        {
            $payment_status = __('รอการชำระเงิน');
        }
        else if ($order->payment_status == 'reconcile')
        {
            $payment_status = __('ชำระเงินแล้ว');
        }
        else
        {
            $payment_status = __('ชำระเงินเรียบร้อยแล้ว');
        }

        $paymentMethod     = $this->getPaymentMethod($order->payment_method);
        $paymentMethodThai = $this->getPaymentMethodThai($order->payment_method);

        $orderApp = PApp::find($order->app_id);
        $appSlug = $orderApp->slug;

        $viewData = compact('order', 'paymentMethod', 'paymentMethodThai', 'payment_status', 'front_url', 'link', 'thank_link', 'data', 'shipments', 'appSlug');
       
        if ($data['lang'] === 'en')
        {
            $template = View::make("messages.emails.en.".$appSlug."_order", $viewData)->render();
        }
        else
        {            
            $template = View::make("messages.emails.th.".$appSlug."_order", $viewData)->render();
        }

        return $template;
    }
}
